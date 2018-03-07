<?php


use Authenticate\SessionUser\SessionUser;
use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\CheckoutLayer;
use Module\Ekom\CheckoutLayerProvider\CheckoutLayerProvider;
use Module\Ekom\Exception\EkomPublicException;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\E;

$out = '';
if (array_key_exists("action", $_GET)) {
    $action = $_GET['action'];

    function getArgument($key, $throwEx = true, $default = null)
    {
        if (array_key_exists($key, $_POST)) {
            $ret = $_POST[$key];
            if ('true' === $ret) {
                $ret = true;
            }
            if ('false' === $ret) {
                $ret = false;
            }
            return $ret;
        }
        if (true === $throwEx) {
            throw new \Exception("Argument not found: $key in \$_POST");
        }
        return $default;
    }


    try {

        switch ($action) {
            case 'bundle.addToCart':
                $bId = getArgument("bundleId");
                $removedProductIds = getArgument("removedProductIds", false, []);
                $api = EkomApi::inst();
                $type = 'success';
                $productId2Qty = EkomApi::inst()->bundleLayer()->addBundleToCart($bId, $removedProductIds);

                $out = [
                    'cartModel' => EkomApi::inst()->cartLayer()->getCartModel(),
                    'productId2Qty' => $productId2Qty,
                ];

                break;
            case 'bundle.getBundleModel':
                $pId = getArgument("productId");
                $removedProductIds = getArgument("removedProductIds", false, []);
                $api = EkomApi::inst();
                $type = 'success';
                $out = EkomApi::inst()->bundleLayer()->getBundleModelByProductId($pId, $removedProductIds);
                break;
            /**
             * Returns a productBox model
             */
            case 'cart.addItem':


                /**
                 * This code has been changed without checking the impact,
                 * it might be broken...
                 */

                $qty = getArgument("qty");
                $pId = getArgument("product_id");
                $cart = EkomApi::inst()->cartLayer();
                $cart->addItem($qty, $pId, $_POST);
                $type = 'success';
                $out = $cart->getCartModel($_POST);

                break;
            case 'cart.removeItem':
                $token = getArgument("token");
                $api = EkomApi::inst();
                $api->cartLayer()->removeItem($token);
                $type = 'success';
                $out = EkomApi::inst()->cartLayer()->getCartModel();

                break;
            case 'cart.updateItemQuantity':
                $qty = getArgument("qty");
                $token = getArgument("token");

                $cart = EkomApi::inst()->cartLayer();
                $res = $cart->updateItemQuantity($token, $qty);

                $type = 'success';
                $out = $cart->getCartModel();
                Hooks::call("Ekom_updateItemQuantity_decorateCartModel", $out, $_POST);

                break;
            case 'user.getAddressInfo':
                if (SessionUser::isConnected()) {

                    $userId = SessionUser::getValue("id");
                    $addressId = getArgument("address_id");

                    if (false !== ($row = EkomApi::inst()->userAddressLayer()->getUserAddressById($userId, $addressId))) {
                        $type = "success";
                        $out = $row;
                    } else {
                        $type = "error";
                        $out = "couldn't access the address";
                    }

                } else {
                    $type = "error";
                    $out = "the user is not connected";
                }
                break;
            case 'user.removeAddress':
                if (SessionUser::isConnected()) {

                    $userAddressLayer = EkomApi::inst()->userAddressLayer();
                    $userId = E::getUserId();
                    $addressId = getArgument("address_id");


                    $userAddressLayer->deleteAddress($userId, $addressId);
                    $type = "success";
                    $out = [
                        "addresses" => $userAddressLayer->getUserAddresses($userId),
                    ];


                } else {
                    $type = "error";
                    $out = "the user is not connected";
                }
                break;
            case 'checkout.setShippingBillingSynced':

                $value = getArgument("value");
                $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
                $checkoutLayer->setShippingAndBillingAreSynced($value);

                $type = "success";
                $out = [
                    "orderModel" => $checkoutLayer->getOrderModel(),
                ];

                break;
            case 'checkout.setCarrierName':


                $name = getArgument("name");


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

                $id = getArgument("id");
                $userId = E::getUserId();
                $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
                $checkoutLayer->setShippingAddressId($id);

                $type = "success";
                $out = [
                    "orderModel" => $checkoutLayer->getOrderModel(),
                ];

                break;
            case 'checkout.setBillingAddressId':

                $id = getArgument("id");
                $userId = E::getUserId();
                $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
                $checkoutLayer->setBillingAddressId($id);

                $type = "success";
                $out = [
                    "orderModel" => $checkoutLayer->getOrderModel(),
                ];

                break;
            case 'checkout.setShippingAndBillingAddressId':

                $id = getArgument("id");
                $userId = E::getUserId();
                $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
                $checkoutLayer->setShippingAndBillingAddressId($id);

                $type = "success";
                $out = [
                    "orderModel" => $checkoutLayer->getOrderModel(),
                ];

                break;
            case 'checkout.setPaymentMethod':

                $id = getArgument("id");
                $paymentMethodOptions = getArgument("options", false, []);
                $userId = EkomApi::inst()->userLayer()->getUserId();
                $checkoutLayer = EkomApi::inst()->ajaxHandlerLayer()->getCheckoutLayer();
                $checkoutLayer->setPaymentMethod($id, $paymentMethodOptions);

                $type = "success";
                $out = [
                    "orderModel" => $checkoutLayer->getOrderModel(),
                ];

                break;
            case 'checkout.updateItemQuantity':
                $qty = getArgument("qty");
                $pId = getArgument("product_id");

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
            default:
                break;
        }

    } catch (\Exception $e) {
        $type = "error";
        XLog::error("[Ekom module] - service/gscp/api: $e");

        if ($e instanceof EkomUserMessageException) {
            $out = $e->getMessage();
        } else {
            $out = "an exception occurred with the message: " . $e->getMessage();
            $out .= ". Please contact the webmaster";
        }

    }

}