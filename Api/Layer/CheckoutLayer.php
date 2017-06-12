<?php


namespace Module\Ekom\Api\Layer;


use Bat\SessionTool;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Utils\E;


/**
 *
 * Important notes for developer
 * ================================
 *
 * If singleAddress mode is used, then the section is the following (index=0):
 *
 *      $_SESSION['ekom.order']["sections"][0]
 *
 *
 */
class CheckoutLayer
{


    /**
     * Returns whether or not the customer should have the ability to
     * manually choose her carrier(s).
     */
    public function hasCarrierChoice()
    {
        $carrierSel = E::conf("carrierSelectionMode");
        if ("manual" === $carrierSel) {
            return true;
        }
        return false;
    }


    /**
     * @param data :
     *      - type: singleAddress
     *      - params: // depend on the type
     *          // params for singleAddress
     *          - address_id
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


                $userLayer = EkomApi::inst()->userLayer();
                $userId = $userLayer->getUserId();
                $address_id = (int)$params['address_id'];
                $address = $userLayer->getUserShippingAddressById($userId, $address_id);
                $cartModel = EkomApi::inst()->cartLayer()->getCartModel();


                /**
                 * note, we use 0 as the only section identifier for single address,
                 * so that if the user goes back and forth, we are ensured to have
                 * only ONE section anyway.
                 */
                $_SESSION['ekom.order']["sections"][0] = [
                    "carrier" => null,
                    "address" => $address,
                    "items" => $cartModel['items'], // tmp?
                ];


                $ret = true;
                if (true === $ret) {
                    $carrierModel = [];
                    $this->applySingleAddressCarrierIfNoChoice($carrierModel);
                    $_SESSION['ekom.order']["sections"][0]['carrier'] = $carrierModel;
                    if (count($carrierModel) > 0) {
                        $this->refreshSummary();
                    }
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


    /**
     *
     */
    public function saveStepPayment(array $data)
    {
        $ret = true;
        // assuming single address technique
        /**
         * todo: adapt for multiple address technique
         */
        if (array_key_exists("paymentId", $data)) {
            $id = $data['paymentId'];
            $info = EkomApi::inst()->paymentLayer()->getSelectableItemById($id);
            $_SESSION['ekom.order']['payment_method'] = $info;
//            $this->refreshSummary();

        } else {
            throw new \Exception("Invalid data structure, key not found: paymentId");
        }
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
                "summary" => null,
                "payment_method" => null,
                "sections" => [],
            ];
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function refreshSummary()
    {
        $cartModel = EkomApi::inst()->cartLayer()->getCartModel();
        unset($cartModel['items']);
        $_SESSION['ekom.order']['summary'] = $cartModel;
    }

    private function applySingleAddressCarrierIfNoChoice(array &$carrierModel)
    {
        $carrierSel = E::conf("carrierSelectionMode");
        if ("fixed:" === substr($carrierSel, 0, 6)) {


            $carrierLayer = EkomApi::inst()->carrierLayer();


            $carrierName = substr($carrierSel, 6);


            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            $carriers = $carrierLayer->getCarriersByShop($shopId);
            if (in_array($carrierName, $carriers, true)) {
                foreach ($carriers as $carrierId => $name) {
                    if ($carrierName === $name) {


                        $carrier = $carrierLayer->getCarrierByName($carrierName);
                        $cartModel = EkomApi::inst()->cartLayer()->getCartModel();

                        $shopAddress = EkomApi::inst()->shopLayer()->getShopPhysicalAddress($shopId);


                        $shippingAddress = $_SESSION['ekom.order']["sections"][0]['address'];


                        $rejected = [];
                        $info = $carrier->handleOrder([
                            "products" => $cartModel['items'],
                            "shopAddress" => $shopAddress,
                            "shippingAddress" => $shippingAddress,
                        ], $rejected);

                        $rej = [];
                        if (count($rejected) > 0) {
                            foreach ($rejected as $id) {
                                $rej[$id] = $this->getCartItemById($cartModel, $id);
                            }
                        }


                        $carrierModel = [
                            "carrier_id" => $carrierId,
                            "carrier_label" => $carrier->getLabel(),
                            "estimated_delivery_date" => (array_key_exists("estimated_delivery_date", $info)) ? $info['estimated_delivery_date'] : null,
                            "shipping_cost" => E::price($info['shipping_cost']),
                            "rejected" => $rejected,
                        ];
                    }
                }


            } else {
                throw new \Exception("The chosen carrier ($carrierName) is not available to this shop ($shopId)");
            }

        } else if ("auto" === $carrierSel) {
            throw new \Exception("not implemented yet");
        } else {
            throw new \Exception("not implemented yet");
            $nbAvailableCarriers = 0;
            if (1 === count($nbAvailableCarriers)) {

            }
        }
    }


    private function getCartItemById($cartModel, $id)
    {
        $id = (int)$id;
        foreach ($cartModel as $info) {
            if ((int)$info['product_id'] === $id) {
                return $info;
            }
        }
        return false;
    }
}
