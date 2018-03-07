<?php


use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\UserNotConnectedException;
use Module\Ekom\ProductSearch\ProductSearchInterface;
use Module\Ekom\Utils\E;
use OnTheFlyForm\Helper\OffProtocolHelper;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;

$out = '';


if (array_key_exists("action", $_GET)) {
    $action = $_GET['action'];

    function getArgument($key, $post = false)
    {
        if (false === $post) {
            if (array_key_exists($key, $_GET)) {
                return $_GET[$key];
            }
            throw new \Exception("Argument not found: $key in \$_GET");
        } else {
            if (array_key_exists($key, $_POST)) {
                return $_POST[$key];
            }
            throw new \Exception("Argument not found: $key in \$_POST");
        }
    }


    switch ($action) {
        case 'user.addToWishlist':
            $pId = getArgument("product_id", true);
            $productDetails = [];
            if (
                array_key_exists('product_details', $_POST) &&
                is_array($_POST['product_details'])
            ) {
                $productDetails = $_POST['product_details'];
            }

            $n = 0;
            $type = 'success';
            $errMsg = "";
            $successMsg = "";
            $hasChanged = false;

            try {
                $hasChanged = EkomApi::inst()->wishListLayer()->addToWishList($pId, $productDetails,null, $n);
                if (false === $hasChanged) {
                    $type = 'error';
                    $errMsg = "Ce produit est déjà dans vos favoris";
                } else {
                    $successMsg = "Le produit a bien été ajouté à vos favoris";
                }

            } catch (\Exception $e) {
                $type = 'error';
                if ($e instanceof UserNotConnectedException) {
                    $errMsg = "Veuillez vous connected d'abord";
                } else {
                    XLog::error("[Ekom Module] - json service: $e");
                    $errMsg = "An unexpected error occurred, please contact the webmaster";
                }
            }

            $out = [
                "type" => $type,
                'nbItems' => $n,
                'errMsg' => $errMsg,
                'successMsg' => $successMsg,
                'hasChanged' => (string)$hasChanged,
            ];
            break;
        /**
         * Returns a productBox model
         */
        case 'getProductInfo':
            $id = getArgument("id");
            $api = EkomApi::inst();
//            Hooks::call("Ekom_prepareJsonService", $action);
            $out = $api->productLayer()->getProductBoxModelByProductId($id, null, null, $_GET);
            break;
        case 'cart.addCoupon':
            $code = getArgument("code", true);
            $force = (int)getArgument("force", true);
            $api = EkomApi::inst();
            $cart = $api->cartLayer();
            $out = $api->couponLayer()->addCouponByCode($code, $cart, $force);

            break;
        case 'cart.removeCoupon':
            $index = getArgument("index", true);
            $api = EkomApi::inst();
            $errors = [];
            $api->couponLayer()->removeCouponByIndex($index);
            $out = EkomApi::inst()->cartLayer()->getCartModel();
            break;
        case 'comment.createComment':
            if (SessionUser::isConnected()) {

                $userId = SessionUser::getValue("id");
                $sData = getArgument("data", true);
                parse_str($sData, $data);

                $productId = $data['product_id'];

                $commentLayer = EkomApi::inst()->productCommentLayer();


                /**
                 * @var $provider OnTheFlyFormProviderInterface
                 */
                $provider = X::get("Core_OnTheFlyFormProvider");
                $form = $provider->getForm("Ekom", "Comment");
                $model = $form->getModel();
                if (true === $form->validate($data, $model)) {

                    if (false !== $commentLayer->insertComment($productId, $data)) {
                        $out = [
                            "type" => "success",
                        ];
                    } else {
                        $out = [
                            "type" => "error",
                            "error" => "An exception occurred, please contact the webmaster.",
                        ];
                    }
                } else {
                    $out = [
                        "type" => "formerror",
                        "model" => $model,
                    ];
                }
            } else {
                $out = [
                    "type" => "error",
                    "error" => "the user is not connected",
                ];
            }
            break;
        /**
         * create/update an address
         *
         * - address_id: if set, update, else create
         */
        case 'user.saveAddress':
        case 'checkout.saveAddress':
            // off protocol
            $out = [];
            if (SessionUser::isConnected()) {
                $userId = SessionUser::getValue("id");
                $sData = getArgument("data", true);
                parse_str($sData, $data);

                $userAddressLayer = EkomApi::inst()->userAddressLayer();

                $addressId = null;
                if (array_key_exists("address_id", $data)) {
                    $addressId = $data["address_id"];
                }

                /**
                 * @var $provider OnTheFlyFormProviderInterface
                 */
                $provider = X::get("Core_OnTheFlyFormProvider");
                $form = $provider->getForm("Ekom", "UserAddress");
                $form->inject($data);

                if (true === $form->validate()) {


                    $data = $form->getData();
                    if (true === $userAddressLayer->createAddress($userId, $data, $addressId)) {
                        $data = $userAddressLayer->getUserAddresses($userId);
                        OffProtocolHelper::success($out, $form, $data);
                        if ('checkout.saveAddress' === $action) {
                            $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
                            $out['orderModel'] = $checkoutLayer->getOrderModel();
                        }
                    } else {
                        $form->setErrorMessage("An exception occurred, the address couldn't be created, please contact the webmaster.");
                        OffProtocolHelper::formError($out, $form);
                    }
                } else {
                    OffProtocolHelper::formError($out, $form);
                }
            } else {
                OffProtocolHelper::error($out, "the user is not connected");
            }
            break;
        case 'product-search':
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
        case 'checkout.placeOrder':
            $out = EkomApi::inst()->ajaxHandlerLayer()->handleCheckoutPlaceOrder();
            break;
        default:
            break;
    }


}