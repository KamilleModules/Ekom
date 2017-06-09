<?php


namespace Module\Ekom\Api\Layer;


use Bat\SessionTool;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Utils\E;

class CheckoutLayer
{

    /**
     * @param data :
     *      - type: singleAddress
     *      - params: // depend on the type
     *          // params for singleAddress
     *          - address_id
     *
     * @return bool
     * @throws EkomApiException
     */
    public function saveStepShipping(array $data)
    {
        $ret = false;
        $type = $data['type'];
        $params = $data['params'];
        switch ($type) {
            case 'singleAddress':

                $address_id = (int)$params['address_id'];
                $cartModel = EkomApi::inst()->cartLayer()->getCartModel();


                /**
                 * note, we use 0 as the only section identifier for single address,
                 * so that if the user goes back and forth, we are ensured to have
                 * only ONE section anyway.
                 */
                $_SESSION['ekom.order']["sections"][0] = [
                    "carrier" => null,
                    "address_id" => $address_id,
                    "items" => $cartModel['items'], // tmp?
                ];

                $ret = true;
                if (true === $ret) {
                    $this->applySingleAddressCarrierIfNoChoice();
                }
                break;
            default:
                throw new EkomApiException("Unknown type: $type");
                break;
        }


        /**
         * Try to see if the carrier is selected automatically,
         * if so, apply the step directly (to save one client round trip in case of an ajax-driven checkout page)
         */

        return $ret;
    }


    public function getOrderModel()
    {
        $this->initOrderModel();
        return $_SESSION['ekom.order'];
    }


    public function initOrderModel()
    {
        SessionTool::start();

        if (false === array_key_exists("ekom.order", $_SESSION)) {
            $_SESSION['ekom.order'] = [
                "payment_method" => null,
                "sections" => [],
            ];
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function applySingleAddressCarrierIfNoChoice()
    {
        $carrierSel = E::conf("carrierSelectionMode");
        if ("fixed:" === substr($carrierSel, 0, 6)) {


            $carrierLayer = EkomApi::inst()->carrierLayer();

            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");

            $carrierName = substr($carrierSel, 6);
            $carriers = $carrierLayer->getCarriersByShop($shopId);
            if (in_array($carrierName, $carriers, true)) {


                $carrier = $carrierLayer->getCarrierByName($carrierName);
                $cartModel = EkomApi::inst()->cartLayer()->getCartModel();
                $productInfos = $cartModel['items'];
                $shopAddress = EkomApi::inst()->shopLayer()->getShopPhysicalAddress($shopId);

                az($productInfos);

                $carrier->handleOrder($productInfos, $shopAddress, $shippingAddress, $rejected);

            } else {
                throw new \Exception("The chosen carrier ($carrierName) is not available to this shop ($shopId)");
            }

        } else if ("auto" === $carrierSel) {

        } else {
            $nbAvailableCarriers = 0;
            if (1 === count($nbAvailableCarriers)) {

            }
        }
        az("oo");
    }
}
