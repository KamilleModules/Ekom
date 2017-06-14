<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;

class OrderLayer
{

    /**
     * @return false|array orderModel,
     *              return false if the user is not connected
     *
     */
    public function getOrderModel()
    {
        if (SessionUser::isConnected()) {
            // start collecting order data
            $this->initOrderModel();


            $checkoutLayer = EkomApi::inst()->checkoutLayer();
            $userLayer = EkomApi::inst()->userLayer();


            $userId = SessionUser::getValue("id");
            $shippingAddresses = $userLayer->getUserShippingAddresses($userId);

            /**
             * false|addressModel
             */
            $billingAddress = $userLayer->getUserBillingAddress($userId);
            $countryId = $userLayer->getUserPreferredCountry();


            /**
             * @var $provider OnTheFlyFormProviderInterface
             */
            $provider = X::get("Core_OnTheFlyFormProvider");
            $form = $provider->getForm("Ekom", "UserAddress");
            $hasCarrierChoice = $checkoutLayer->hasCarrierChoice();
            $paymentMethodBlocks = EkomApi::inst()->paymentLayer()->getPaymentMethodBlockModels();


            return [
                "billingAddress" => $billingAddress,
                "shippingAddresses" => $shippingAddresses,
                "defaultCountry" => $countryId,
                "shippingAddressFormModel" => $form->getModel(),
                "hasCarrierChoice" => $hasCarrierChoice,
                "paymentMethodBlocks" => $paymentMethodBlocks,
//                            "shippingType" => "singleAddressShipping", // singleAddressShipping|multipleAddressShipping // implicitly: it's singleAddressShipping, unless otherwise specified
            ];
        }
        return false;
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private function initOrderModel()
    {
        SessionTool::start();

        a($_SESSION);
        if ('singleAddress') {
            if (false === array_key_exists("ekom.order.singleAddress", $_SESSION)) {
                /**
                 *
                 * class-modules/Ekom/doc/ekom-checkout-synopsis.md
                 *
                 *
                 * - ekom.order.singleAddress
                 * ----- billing_address_id
                 * ----- shipping_address_id
                 * ----- carrier_id
                 * ----- ?carrier_options array of key => value, depending on the carrier (read relevant carrier doc for more info)
                 * ----- payment_method_id
                 * ----- ?payment_method_options: array of key => value, depending on the payment method (read relevant payment method doc for more info)
                 *
                 *
                 */
                $userLayer = EkomApi::inst()->userLayer();
                $userId = $userLayer->getUserId();
                $billingAddressId = $userLayer->getUserBillingAddressId($userId);
                if (false === $billingAddressId) {
                    $billingAddressId = null;
                }

                $shippingAddressId = $userLayer->getPreferredShippingAddressId($userId);



                $_SESSION['ekom.order.singleAddress'] = [
                    "billing_address_id" => $billingAddressId,
                    "shipping_address_id" => null,
                    "carrier_id" => null,
                    "payment_method_id" => null,
                ];
            }
        } else {
            throw new \Exception("Not implemented yed");
        }
    }
}
