<?php


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Bat\ValidationTool;
use Core\Services\A;
use Core\Services\Hooks;
use Core\Services\X;
use Ecp\EcpServiceUtil;
use Ecp\Output\EcpOutputInterface;
use Kamille\Services\XConfig;
use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\CouponLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\NewsletterLayer;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\WishListLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Back\WidgetModel\Dashboard\DefaultDashboardModel;
use Module\Ekom\Ecp\EkomEcpServiceUtil;
use Module\Ekom\Helper\CheckoutHelper;
use Module\Ekom\ProductSearch\ProductSearchInterface;
use Module\Ekom\SokoForm\UserAddress\UserAddressSokoForm;
use Module\Ekom\Utils\Checkout\CheckoutUtil;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcess;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\Morphic\EkomMorphicAdminListRenderer;
use QuickPdo\QuickPdo;
use SafeUploader\SafeUploader;
use SafeUploader\Tool\SafeUploaderHelperTool;
use SokoForm\Form\SokoFormInterface;

/**
 * Please read "Create your own ECP service, preserve the harmony" section from the
 * doc/apis/ekom-service-api.md document first.
 */
$out = EkomEcpServiceUtil::executeProcess(function ($action, $intent, EcpOutputInterface $output) {

    $out = [];
    switch ($action) {
        //--------------------------------------------
        // BUNDLE (product sold together)
        //--------------------------------------------
        case 'bundle.addToCart':
            $bId = EcpServiceUtil::get("bundleId");
            $removedProductIds = EcpServiceUtil::get("removedProductIds", false, []);
            $api = EkomApi::inst();
            $type = 'success';
            $productId2Qty = EkomApi::inst()->bundleLayer()->addBundleToCart($bId, $removedProductIds);

            $out = [
                'cartModel' => EkomApi::inst()->cartLayer()->getCartModel(),
                'productId2Qty' => $productId2Qty,
            ];
            break;
        case 'bundle.getBundleModel':
            $pId = EcpServiceUtil::get("productId");
            $removedProductIds = EcpServiceUtil::get("removedProductIds", false, []);
            $api = EkomApi::inst();
            $type = 'success';
            $out = EkomApi::inst()->bundleLayer()->getBundleModelByProductId($pId, $removedProductIds);
            break;
        //--------------------------------------------
        // CART
        //--------------------------------------------
        case 'cart.addItem':
            $pool = $_POST;
            $quantity = EcpServiceUtil::get("quantity");
            $pId = EcpServiceUtil::get("product_id");
            $cart = EkomApi::inst()->cartLayer();
            $details = EcpServiceUtil::get("details", false, []);
            $boxModel = ProductBoxLayer::getProductBoxByProductId($pId, $details);
            $pool['details'] = $boxModel['productDetails'];

            $cart->addItem($quantity, $pId, $pool);
            $out['cartModel'] = $cart->getCartModel();
            break;
        case 'cart.updateItemQuantity':
            $qty = EcpServiceUtil::get("quantity");
            $token = EcpServiceUtil::get("token");
            $cart = EkomApi::inst()->cartLayer();
            $cart->updateItemQuantity($token, $qty);
            $out['cartModel'] = $cart->getCartModel();
//            Hooks::call("Ekom_updateItemQuantity_decorateCartModel", $out, $_POST); // deprecated?

            break;
        case 'cart.removeItem':
            $token = EcpServiceUtil::get("token");
            $cart = EkomApi::inst()->cartLayer();
            $cart->removeItem($token);
            $out['cartModel'] = $cart->getCartModel();

            break;
        //--------------------------------------------
        // COUPON
        //--------------------------------------------
        case 'cart.addCoupon':
            $code = EcpServiceUtil::get("code");
            if (!empty($code)) {
                $cart = EkomApi::inst()->cartLayer();
                $cartModel = $cart->getCartModel();
                $error = null;
                $couponInfo = null;
                if (true === CouponLayer::couponIsValidByCode($code, $cartModel, $error, $couponInfo)) {
                    $cart->addCoupon($couponInfo['id']);
                    $cartModel = $cart->getCartModel(); // acknowledge new coupons
                    $out['cartModel'] = $cartModel;
                } else {
                    switch ($error) {
                        case "invalid":
                        case "notFound":
                            $errorMessage = "Ce coupon n'est pas valide";
                            break;
                        case "mismatch":
                            $errorMessage = "Ce coupon ne peut pas s'appliquer à votre panier";
                            break;
                        default:
                            $errorMessage = "Ce coupon n'est pas valide";
                            break;
                    }
                    $output->error($errorMessage);
                }
            }
            break;
        case 'cart.removeCoupon':
            $code = EcpServiceUtil::get("code");
            $cart = EkomApi::inst()->cartLayer();
            $cart->removeCoupon($code);
            $out['cartModel'] = $cart->getCartModel();
            break;
        //--------------------------------------------
        // USER
        //--------------------------------------------
        case 'user.addProductToWishlist':

            $productId = EcpServiceUtil::get("product_id");
            $productDetails = EcpServiceUtil::get("details", false, []);
            $n = 0;

            if (true === E::userIsConnected()) {
                $hasChanged = EkomApi::inst()->wishListLayer()->addToWishList($productId, $productDetails, null, $n);

                $uriWishList = E::link("Ekom_customerWishList");

                if (false === $hasChanged) {
                    $output->error('
Ce produit est déjà dans vos favoris.<br>
<a href="' . $uriWishList . '">Voir mes favoris</a>
');
                } else {
                    $output->success('
Le produit a bien été ajouté à vos favoris.<br>
<a href="' . $uriWishList . '">Voir mes favoris</a>
                    ');
                }
            } else {
                $output->error("Veuillez vous connecter d'abord");
            }

            $out['nbItems'] = $n;
            break;
        case 'user.removeWishlist':
            $n = 0; // should start with the number of items per user, but...
            if (true === E::userIsConnected()) {
                $userId = E::getUserId();
                WishListLayer::removeUserWishlist($userId);
                $n = 0;
            } else {
                $output->error("Veuillez vous connecter d'abord");
                $n = 0;
            }
            $out['nbItems'] = $n;

            break;
        case 'user.removeWishlistItem':
            $n = 0; // should start with the number of items per user, but...
            if (true === E::userIsConnected()) {
                $productId = EcpServiceUtil::get("product_id");
                $userId = E::getUserId();
                WishListLayer::removeUserWishlistItem($userId, $productId);
                $n = WishListLayer::getNbUserWishItems($userId);
            } else {
                $output->error("Veuillez vous connecter d'abord");
                $n = 0;
            }
            $out['nbItems'] = $n;

            break;
        case 'user.subscribeToNewsletter':

            $email = EcpServiceUtil::get("email");
            if (NewsletterLayer::isRegistered($email)) {
                $output->error("Vous êtes déjà inscrit(e) à cette newsletter");
            } else {
                if (ValidationTool::isEmail($email)) {

                    NewsletterLayer::registerEmail($email);
                    $output->success("Merci. Vous êtes maintenant inscrit(e) à cette newsletter");
                } else {
                    $output->error("Cet email n'est pas valide");
                }
            }

            break;
//        case 'user.getAddressInfo':
//            if (SessionUser::isConnected()) {
//
//                $userId = SessionUser::getValue("id");
//                $addressId = EcpServiceUtil::get("address_id");
//
//                if (false !== ($row = EkomApi::inst()->userAddressLayer()->getUserAddressById($userId, $addressId))) {
//                    $type = "success";
//                    $out = $row;
//                } else {
//                    $type = "error";
//                    $out = "couldn't access the address";
//                }
//
//            } else {
//                $type = "error";
//                $out = "the user is not connected";
//            }
//            break;
        case 'user.getAddressForm':

            if (E::userIsConnected()) {


                /**
                 * @todo-ling: doc of fetch/post pattern below
                 *
                 * Two modes:
                 * fetch|post
                 *
                 * default=post
                 *
                 * First mode, fetch, is to get the form model (and or html).
                 * It's either a filled form (update) or an empty form (insert).
                 * This is triggered by the sent of the ric (typically primaryKey) which triggers the filled form.
                 * Without the ric being sent, the empty form is returned.
                 *
                 * Then second mode: post.
                 * Post handles actions when the form is posted.
                 * Again, we differentiate between insert and update behaviour using the ric trigger.
                 * The isSuccess is part or the return in this case.
                 * Its value is a boolean.
                 *
                 * In this particular implementation, in fetch mode we return formHtml (that's the idea, the name
                 * might have been changed), and in post mode, what we return depends on whether the form
                 * was a success or a failure.
                 * If it's a success, we return the checkoutHtml (which will replace the current checkout process,
                 * just like that), and if it's a failure, then we return the form html again (with the embedded
                 * errors inside of it) until the form is valid.
                 *
                 *
                 *
                 *
                 *
                 */
                $mode = EcpServiceUtil::get("mode", false, "post");
                $prefilled = EcpServiceUtil::get("prefilled", false, "0");


                $form = UserAddressSokoForm::getForm();
                $isSuccess = false;
                $context = [];
                $userId = SessionUser::getValue("id");
                $addressId = EcpServiceUtil::get("shipping_address_id", false, null);
                if (null === $addressId) {
                    $addressId = EcpServiceUtil::get("billing_address_id", false, null);
                }


                if (true === (bool)$prefilled) {
                    $_POST = [
                        "first_name" => "Johnny",
                        "last_name" => "Hallyday",
                        "phone" => "05 41 78 51 34",
                        "phone_prefix" => "33",
                        "address" => "6 rue Napoléon",
                        "city" => "Marne-la-Vallée",
                        "postcode" => "77420",
                        "country" => "FR",
                    ];
                }


                if (null !== $addressId) {
                    $userAddress = UserAddressLayer::getAddressById($userId, $addressId);
                    $context = array_replace($userAddress, $_POST);
                } else {
                    $context = $_POST;
                }


                if ('fetch' === $mode) {
                    $form->inject($context);
                } else {
                    $form->process(function (array $data, SokoFormInterface $form) use (&$isSuccess, $mode, $userId, $addressId) {
                        $data['active'] = 1;
                        $data['is_default_shipping_address'] = (int)$data['is_default_shipping_address'];
                        $data['is_default_billing_address'] = (int)$data['is_default_billing_address'];
                        $isSuccess = EkomApi::inst()->userAddressLayer()->createAddress($userId, $data, $addressId);
                    }, $context);
                }

                $formModel = $form->getModel();
                $out = [
                    "addressId" => $addressId,
                    "isSuccess" => $isSuccess,
                    "form" => $formModel,
                    "html" => "", // overridden by hook below
                ];
            } else {
                $out = ClawsWidgetError::create()
                    ->setErrorMessage("The user is not connected")
                    ->getModel();
            }
            break;
        case 'user.getAddresses':
            $type = EcpServiceUtil::get("type");
            if (E::userIsConnected()) {
                $langId = E::getLangId();
                $userId = E::getUserId();
                $out = [
                    'addresses' => UserAddressLayer::getUserAddresses($userId, $langId),
                    'type' => $type,
                ];
            } else {
                $out = ClawsWidgetError::create()
                    ->setErrorMessage("The user is not connected")
                    ->getModel();
            }
            break;
        case
        'user.removeAddress':
            if (SessionUser::isConnected()) {

                $userAddressLayer = EkomApi::inst()->userAddressLayer();
                $userId = E::getUserId();
                $addressId = EcpServiceUtil::get("address_id");
                $userAddressLayer->deleteAddress($userId, $addressId);
                $out = [
                    "addresses" => $userAddressLayer->getUserAddresses($userId),
                ];


            } else {
                $type = "error";
                $out = "the user is not connected";
            }
            break;
        //--------------------------------------------
        // CHECKOUT NEW
        //--------------------------------------------
        case 'checkout.switchStep':
            /**
             * even though we don't use it in this scope (the execute method will use it internally)
             * we block the user if he/she doesn't provides it
             */
            EcpServiceUtil::get("_step");
            $out['checkoutModel'] = CheckoutUtil::getCurrentCheckoutProcessModel();
            break;
        case 'checkout.updateStep':
            CheckoutHelper::updateCurrentCheckoutData();
            $out['checkoutModel'] = CheckoutUtil::getCurrentCheckoutProcessModel();
            break;
        case 'checkout.updateCarrier':
            $out['checkoutModel'] = CheckoutUtil::getCurrentCheckoutProcessModel();
            break;
        case 'checkout.placeOrder':

            CheckoutHelper::updateCurrentCheckoutData();

            $checkoutData = CheckoutProcess::getCheckoutData();


            $checkoutData['user_id'] = E::getUserId();
            $checkoutData['shop_id'] = E::getShopId();
            $checkoutData['lang_id'] = E::getLangId();
            $checkoutData['currency_id'] = E::getCurrencyId();


            $route = CheckoutUtil::getCheckoutThankYouRoute();


            $cartModel = CheckoutUtil::getCurrentCartLayer()->getCartModel();
            $checkoutUtil = CheckoutUtil::getCurrentCheckoutOrderUtil();
            $orderId = $checkoutUtil
                ->setTestMode(false)
                ->placeOrder($checkoutData, $cartModel);


            $out['ok'] = 1;
            $out['order_id'] = $orderId;
            $out['uriRedirect'] = E::link($route) . "?order_id=$orderId";

            break;
        //--------------------------------------------
        // CHECKOUT OLD
        //--------------------------------------------
        case 'checkout.setShippingBillingSynced':

            $value = EcpServiceUtil::get("value");
            $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
            $checkoutLayer->setShippingAndBillingAreSynced($value);

            $type = "success";
            $out = [
                "orderModel" => $checkoutLayer->getOrderModel(),
            ];

            break;
        case 'checkout.setCarrierName':


            $name = EcpServiceUtil::get("name");


            /**
             * @todo-ling: sorry, this is the consequence of misconception
             * started in ShippingOrderBuilderStep.
             *
             */
            $userId = E::getUserId();
            $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
            $checkoutLayer->setCarrierName($name);

            $type = "success";
            $out = [
                "orderModel" => $checkoutLayer->getOrderModel(),
            ];

            break;
        case 'checkout.setShippingAddressId':

            $id = EcpServiceUtil::get("id");
            $userId = E::getUserId();
            $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
            $checkoutLayer->setShippingAddressId($id);

            $type = "success";
            $out = [
                "orderModel" => $checkoutLayer->getOrderModel(),
            ];

            break;
        case 'checkout.setBillingAddressId':

            $id = EcpServiceUtil::get("id");
            $userId = E::getUserId();
            $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
            $checkoutLayer->setBillingAddressId($id);

            $type = "success";
            $out = [
                "orderModel" => $checkoutLayer->getOrderModel(),
            ];

            break;
        case 'checkout.setShippingAndBillingAddressId':

            $id = EcpServiceUtil::get("id");
            $userId = E::getUserId();
            $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
            $checkoutLayer->setShippingAndBillingAddressId($id);

            $type = "success";
            $out = [
                "orderModel" => $checkoutLayer->getOrderModel(),
            ];

            break;
        case 'checkout.setPaymentMethod':

            $id = EcpServiceUtil::get("id");
            $paymentMethodOptions = EcpServiceUtil::get("options", false, []);
            $userId = EkomApi::inst()->userLayer()->getUserId();
            $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
            $checkoutLayer->setPaymentMethod($id, $paymentMethodOptions);

            $type = "success";
            $out = [
                "orderModel" => $checkoutLayer->getOrderModel(),
            ];

            break;
        case 'checkout.updateItemQuantity':
            $qty = EcpServiceUtil::get("qty");
            $pId = EcpServiceUtil::get("product_id");

            $api = EkomApi::inst();
            $errors = [];
            $res = $api->cartLayer()->updateItemQuantity($pId, $qty, $errors);
            if (false === $res) {
                $type = 'error';
                $out = $errors;
            } else {
                $type = 'success';
                $out = EkomApi::inst()->checkoutLayer()->getOrderModel();
            }
            break;
        //--------------------------------------------
        // SEARCH
        //--------------------------------------------
        case 'search.product':
            $query = "";
            if (array_key_exists("query", $_GET)) {
                $query = $_GET['query'];
            }
            /**
             * @var $pSearch ProductSearchInterface
             */
            $pSearch = X::get("Ekom_productSearch");
            $out = $pSearch->getResults($query);
            break;
        //--------------------------------------------
        // PRODUCT
        //--------------------------------------------
        case 'product.getInfo':
            $pId = EcpServiceUtil::get("product_id");
            $details = EcpServiceUtil::get("details", false, []);
            $out['boxModel'] = ProductBoxLayer::getProductBoxByProductId($pId, $details);
            break;
        //--------------------------------------------
        // BACK
        //--------------------------------------------
        case 'back.morphic':
            $actionType = EcpServiceUtil::get("actionType");
            switch ($actionType) {
                case "delete":
                case "fetch":

                    $viewId = EcpServiceUtil::get('viewId');
                    $context = EcpServiceUtil::get("context", false, []);
                    $config = A::getMorphicListConfig('Ekom', $viewId, $context);
                    $table = $config['table'];
                    $sort = EcpServiceUtil::get("sort", false, []);
                    $filters = EcpServiceUtil::get("filters", false, []);
                    $page = (int)EcpServiceUtil::get("page");
                    $nipp = EcpServiceUtil::get("nipp");
                    $params = [
                        'page' => $page,
                        'sort' => $sort,
                        'filters' => $filters,
                        'nipp' => $nipp,
                    ];

                    MorphicHelper::setListParameters($viewId, $params);

                    $parameters = [];
                    if ("delete" === $actionType) {
                        $parameters = EcpServiceUtil::get('params', false, []);
                        if (array_key_exists("rows", $parameters)) {
                            $rows = $parameters['rows'];
                            $ric = $config['ric'];
                            $whereCondsGen = function (array $row) use ($ric) {
                                $ret = [];
                                foreach ($ric as $col) {
                                    $ret[] = [$col, "=", $row[$col]];
                                }
                                return $ret;
                            };
                            foreach ($rows as $row) {
                                QuickPdo::delete($table, call_user_func($whereCondsGen, $row));
                            }


                        } else {
                            $output->error("rows not defined");
                            return;
                        }
                    }

                    $out['view'] = EkomMorphicAdminListRenderer::create()->renderByConfig($config, $params);
                    break;
                default:
                    $output->error("Unknown actionType: $actionType");
                    break;
            }
            break;
        case 'back.selectShopId':
            $shopId = EcpServiceUtil::get("shop_id");
            if (false !== ($host = ShopLayer::getHostById($shopId))) {
                $shopInfo = [
                    'shop_id' => $shopId,
                    'shop_host' => $host,
                ];
                EkomNullosUser::setShopInfo($shopInfo);
                $out['shopInfo'] = $shopInfo;
            } else {
                $output->error("Invalid shop id: $shopId");
            }
            break;
        case 'back.selectCurrencyId':

            $shopId = EkomNullosUser::getEkomValue("shop_id", false);
            $currencyId = EcpServiceUtil::get("currency_id");
            if ($shopId) {
//                    ShopLayer::setBaseCurrency($shopId, $currencyId);
                if (false !== ($currencyInfo = CurrencyLayer::getCurrencyInfoById($currencyId, $shopId))) {
                    $info = [
                        'currency_id' => $currencyId,
                        'currency_iso_code' => $currencyInfo['iso_code'],
                        'currency_exchange_rate' => $currencyInfo['exchange_rate'],
                    ];
                    EkomNullosUser::setCurrencyInfo($info);
                    $out['currencyInfo'] = $info;
                } else {
                    $output->error("Invalid currency id: $currencyId");
                }
            } else {
                $output->error("Choose the shop first");
            }
            break;
        case 'back.selectLangId':
            $langId = EcpServiceUtil::get("lang_id");
            if (false !== ($isoCode = LangLayer::getIsoCodeById($langId))) {
                $info = [
                    'lang_id' => $langId,
                    'lang_iso_code' => $isoCode,
                ];
                EkomNullosUser::setLangInfo($info);
                $out['langInfo'] = $info;
            } else {
                $output->error("Invalid lang id: $langId");
            }
            break;
        case 'back.fancy-tree.category':
            if (true === EkomNullosUser::isConnected()) {


                $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
                $langId = (int)EkomNullosUser::getEkomValue("lang_id");


                function updateCategories(array $cats, array &$ret)
                {
                    foreach ($cats as $k => $cat) {

                        $children = [];
                        if (count($cat['children']) > 0) {
                            updateCategories($cat['children'], $children);
                            $cat['folder'] = true;
                            $cat['children'] = $children;
                        } else {
                            $cat['folder'] = false;
                        }
                        $cat['title'] = $cat['label'];
                        $ret[$k] = $cat;
                    }
                }


                $o = new CategoryLayer();
                $a = $o->getSubCategoriesByName("home", -1, "", $shopId, $langId, true);

                $ret = [];
                updateCategories($a, $ret);

                $out = $ret;
            } else {
                $output->error("nullos user not connected");
            }
            break;
        case 'back.move-category':

            if (true === EkomNullosUser::isConnected()) {
                $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
                $langId = (int)EkomNullosUser::getEkomValue("lang_id");

                $sourceId = EcpServiceUtil::get("source_id");
                $targetId = EcpServiceUtil::get("target_id");
                $mode = EcpServiceUtil::get("mode");


                $res = CategoryLayer::moveCategory($sourceId, $targetId, $mode, $shopId, $langId);
                $out["result"] = $res;


            } else {
                $output->error("nullos user not connected");
            }
            break;
        case 'back.delete-category':

            if (true === EkomNullosUser::isConnected()) {
                $shopId = (int)EkomNullosUser::getEkomValue("shop_id");

                $categoryId = EcpServiceUtil::get("category_id");
                CategoryLayer::deleteCategory($categoryId, $shopId);

            } else {
                $output->error("nullos user not connected");
            }
            break;
        case 'back.dashboard-gui':

            if (true === EkomNullosUser::isConnected()) {

                $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
                $graph = EcpServiceUtil::get("graph");
                $dateStart = EcpServiceUtil::get("date_start");
                $dateEnd = EcpServiceUtil::get("date_end");
                $model = DefaultDashboardModel::getModel($dateStart, $dateEnd, $shopId, [
                    "mode" => 'ajax',
                    "graph" => $graph,
                ]);


                $out = $model;


            } else {
                $output->error("nullos user not connected");
            }
            break;
        //--------------------------------------------
        // AUTOCOMPLETE
        //--------------------------------------------
        case 'auto.address':

            if (EkomNullosUser::isConnected()) {

                $term = EcpServiceUtil::get("term", true, null, $_GET);
                $rows = QuickPdo::fetchAll("
select 
a.id,
concat(
  a.id, 
  '. ',
  a.first_name, 
  ' ',
  a.last_name, 
  ' ',
  a.address, 
  ' ',
  a.postcode, 
  ' ',
  a.city, 
  ' ',
  UPPER(l.label)
  ) as label
from ek_address a 
inner join ek_country c on c.id=a.country_id
inner join ek_country_lang l on l.country_id=c.id

where 
l.label like :search
or c.iso_code like :search
or a.first_name like :search
or a.last_name like :search
or a.address like :search
or a.city like :search
or a.postcode like :search


", [
                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
                ]);


                $out = $rows;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        case 'auto.category':

            if (EkomNullosUser::isConnected()) {

                $shopId = (int)EkomNullosUser::getEkomValue("shop_id");

                $term = EcpServiceUtil::get("term", true, null, $_GET);
                $rows = QuickPdo::fetchAll("
select 
id,
concat(
  id, 
  '. ',
  `name`
  ) as label
from ek_category  
where 
`name` like :search
and shop_id=$shopId
", [
                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
                ]);


                $out = $rows;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        case 'auto.discount':

            if (EkomNullosUser::isConnected()) {

                $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
                $langId = (int)EkomNullosUser::getEkomValue("lang_id");

                $term = EcpServiceUtil::get("term", true, null, $_GET);
                $rows = QuickPdo::fetchAll("
select 
d.id,
l.label
from ek_discount d 
inner join ek_discount_lang l on l.discount_id=d.id
where 
l.label like :search
and d.shop_id=$shopId
and l.lang_id=$langId
", [
                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
                ]);

                $out = $rows;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        case 'auto.product':

            if (EkomNullosUser::isConnected()) {

                $term = EcpServiceUtil::get("term", true, null, $_GET);

                $rows = QuickPdo::fetchAll("
select
p.id,
concat( 
case when pl.label is not null and pl.label != '' 
then
concat (pl.label, '. ')
else 
''
end, 
concat ('ref=', p.reference)
) as label

from ek_product p
left join ek_product_lang pl on pl.product_id=p.id

where 
p.reference like :search
or pl.label like :search
or p.id like :search
", [
                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
                ]);

                $out = $rows;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
//        /**
//         * Generally, you want to use auto.product_with_label when you can (it's a better version than auto.product)
//         * The reason why you sometimes use auto.product is that when you create the store, the lang might not exist
//         * for a product.
//         *
//         * In other words, if you know that lang exist for products, then use auto.product_with_label.
//         */
//        case 'auto.product_with_label':
//
//            if (EkomNullosUser::isConnected()) {
//
//                $term = EcpServiceUtil::get("term", true, null, $_GET);
//                $langId = EkomNullosUser::getEkomValue("lang_id");
//                $rows = QuickPdo::fetchAll("
//select
//p.id,
//concat(pl.label,p.reference) as label
//
//from ek_product p
//left join ek_product_lang pl on pl.product_id=p.id
//
//where
//p.reference like :search
//or p.id like :search
//or p.id like :search
//
//", [
//                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
//                ]);
//
//                $out = $rows;
//            } else {
//                $output->error("ekom nullos user not connected");
//            }
//            break;
        case 'auto.product_card':

            if (EkomNullosUser::isConnected()) {

                $langId = (int)EkomNullosUser::getEkomValue("lang_id");

                $term = EcpServiceUtil::get("term", true, null, $_GET);

                $rows = QuickPdo::fetchAll("
select 
product_card_id as id,
concat (product_card_id, '. ', label) as label
from ek_product_card_lang  
where 
label like :search
and lang_id=$langId
", [
                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
                ]);

                $out = $rows;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        case 'auto.tag':

            if (EkomNullosUser::isConnected()) {

                $langId = (int)EkomNullosUser::getEkomValue("lang_id");

                $term = EcpServiceUtil::get("term", true, null, $_GET);

                $rows = QuickPdo::fetchAll("
select 
t.id as id,
concat (t.id, '. ', t.name) as label
from ek_tag t  
where 
(
t.id like :search
or t.name like :search
)
#and lang_id=$langId
", [
                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
                ]);

                $out = $rows;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        case 'auto.user':

            if (EkomNullosUser::isConnected()) {

                $term = EcpServiceUtil::get("term", true, null, $_GET);

                $rows = QuickPdo::fetchAll("
select 
id,
concat (
CASE WHEN first_name != '' OR last_name != ''
THEN
concat(first_name, ' ', last_name, ':')
ELSE
''
END,
email,
CASE WHEN pseudo != ''
THEN
concat(' (', pseudo, ')')
ELSE
''
END 



) as label
from ek_user  
where 
first_name like :search
or last_name like :search
or email like :search
or pseudo like :search
", [
                    "search" => '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%',
                ]);

                $out = $rows;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        //--------------------------------------------
        // BACK REACTIVE COMPONENT (see backoffice brainstorm for more info)
        //--------------------------------------------
        case "back.reactive.feature_value":
            if (EkomNullosUser::isConnected()) {
                $featureId = EcpServiceUtil::get("feature_id");
                $langId = (int)EkomNullosUser::getEkomValue("lang_id");
                $items = FeatureLayer::getValueItems($langId, $featureId);
                $out = $items;
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        //--------------------------------------------
        // SAFE UPLOADER PATTERN
        //--------------------------------------------
        case "upload_handler":
            if (EkomNullosUser::isConnected()) { // only bo feature for now
                if (array_key_exists('profile_id', $_GET)) {


                    $payload = (array_key_exists("payload", $_GET)) ? $_GET['payload'] : [];
                    $profileId = $_GET['profile_id'];


                    // upload the file
                    $o = SafeUploader::create()
                        ->setErrorMode('array')
                        ->setConfigurationFile(XConfig::get("Ekom.safeUploadConfigFile", null, true))
                        ->uploadPhpFile($profileId, null, $payload);


                    $errors = $o->getErrors();
                    if (count($errors)) {
                        $output->error("The following errors occurred: " . ArrayToStringTool::toPhpArray($errors));
                    } else {
                        $realPath = $o->getUploadedFilePath();
                        $realPaths = $o->getUploadedFilePaths();
                        $out = [
                            'path' => $realPath,
                        ];
                        /**
                         * now if it's insert mode,
                         * we also want to save the random string ric into the session.
                         */
                        $isTmp = (bool)$payload['isTmp']; // isTmp is defined and passed by the SokoSafeUploadControl form control
                        if (true === $isTmp) {
                            SafeUploaderHelperTool::setTemporaryValue($profileId, $realPaths, $payload['ric']);
                        }
                    }
                } else {
                    $output->error("profile_id not found in _GET");
                }
            } else {
                $output->error("ekom nullos user not connected");
            }
            break;
        default:
            break;
    }


    Hooks::call("Ekom_Ecp_decorateOutput", $out, $action, $intent);

    return $out;
});

