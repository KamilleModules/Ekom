<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\X;
use Ekom\Carrier\MockCarrier;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Carrier\CarrierInterface;
use Module\Ekom\Carrier\Collection\CarrierCollection;
use Module\Ekom\Carrier\Collection\CarrierCollectionInterface;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class CarrierLayerOld
{

    /**
     * Returns whether or not the customer should have the ability to
     * manually choose her carrier(s).
     * @return bool
     */
    public function useSingleCarrier()
    {
        $carrierSel = E::conf("carrierSelectionMode");
        if ("manual" === $carrierSel) {
            return false;
        }
        return true;
    }


    public function getDefaultCarrierName()
    {

        if (true === $this->useSingleCarrier()) {
            if (false !== ($info = $this->getSingleCarrier())) {
                return $info[0];
            }
        } else {
            /**
             * If there is a choice, we take the first choice available
             * Note: the algorithm could be improved.
             */
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            $carriers = $this->getCarriersByShop($shopId);
            if (count($carriers) > 0) {
                return current($carriers);
            } else {
                XLog::error("[Ekom module] - CarrierLayer: no carrier found at all for shop $shopId");
            }
        }
        return null;
    }


    /**
     * @return false|int
     */
    public function getCarrierIdByName($name)
    {

        return A::cache()->get("Ekom.CarrierLayer.getCarrierIdByName.$name", function () use ($name) {

            if (false !== ($row = QuickPdo::fetch("
select c.id 
from ek_carrier c 
inner join ek_shop_has_carrier h on h.carrier_id=c.id 
where c.name=:zename
         
         
         
        ", [
                    "zename" => $name,
                ]))
            ) {
                return (int)$row['id'];
            }
            return false;
        }, [
            "ek_carrier",
        ]);
    }


    /**
     * Return the carrier only if it's available to the given shop.
     * If the shop isn't given, the current shop is used.
     *
     * @param $name
     * @return false|CarrierInterface
     */
    public function getCarrierByName($name, $shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }

        $carriers = $this->getCarriersByShop($shopId);
        if (in_array($name, $carriers, true)) { // available to the shop
            $coll = X::get("Ekom_getCarrierCollection");
            /**
             * @var $coll CarrierCollection
             */
            $ret = $coll->getCarrier($name);
            if (false === $ret) {
                XLog::error("[Ekom module] - CarrierLayer: carrier $name found in database, but not as a class in the fileSystem");
            }
            return $ret;
        }
        return false;
    }


    /**
     * @param array $productInfos , array of productId => productInfo,
     * each productInfo has at least the following keys:
     *
     * - product_id: int, the product id
     * - weight: float, the product weight
     *
     * Note: if you pass the cart model items as the productInfos, it should work...
     *
     *
     *
     * @return false|array with the following structure:
     *          - sections: array of carrierName => sectionInfo, each sectionInfo is an array with the following structure:
     *                  - shippingCost: formatted shipping cost
     *                  - productsInfo: an array of product_id => productInfo
     *                          Each productInfo has the same structure as the passed productInfo.
     *          - notHandled: the array of not handled productInfo (productId => productInfo)
     *          - isEstimate: bool, whether or not the costs are just an estimate or the real shipping costs
     *          - totalShippingCost: string, formatted amount of shipping cost, sum of all sections' shipping costs.
     *
     *
     */
    public function estimateShippingCosts(array $productInfos)
    {
        /**
         * To estimate the shipping cost,
         * we need amongst other things a shipping address and the shop address (maybe not used but we provide it anyway).
         *
         *
         * First, we check whether or not the user is filling the checkout form.
         * If so, we can use the user's choices.
         * If not, we use the following default algorithm:
         *      - If the user is connected and has a shipping address, we use her shipping address.
         *      - If the user is connected and has no shipping address, or if the user is not connected, we use the shop address.
         *      - If the shop has no address, we cannot estimate the shipping costs, and return false, but that's a configuration error and it will be logged.
         *
         */
        $checkoutLayer = EkomApi::inst()->checkoutLayer();


        $shopAddress = EkomApi::inst()->shopLayer()->getShopPhysicalAddress();
        if (false === $shopAddress) {
            $msg = "[Ekom module] - CarrierLayer.estimateShippingCosts: ekom config error, shop address is not defined";
            XLog::error($msg);
            /**
             * Note: for now I don't throw exception because maybe the carrier instance doesn't need the shop address
             * to do its job.
             */
            $shopAddress = null;
        }
        $shippingAddress = null;
        $carriers = [];


        if (true === E::userIsConnected()) {

            if (true === $checkoutLayer->hasCurrentSessionOrder()) {
                $shippingInfo = $checkoutLayer->getShippingInfo();
                if (null !== $shippingInfo['carrier_name']) {

                    EkomApi::inst()->initWebContext();
                    $shopId = ApplicationRegistry::get("ekom.shop_id");
                    $userId = E::getUserId();

                    $carrierName = $shippingInfo['carrier_name'];
                    $shipping_address_id = $shippingInfo['shipping_address_id'];
                    $shippingAddress = EkomApi::inst()->userAddressLayer()->getUserAddressById($userId, $shipping_address_id);

                    $instance = $this->getCarrierByName($carrierName);
                    $carriers[$carrierName] = $instance;

                }
            }
        }


        /**
         * No sensible info found, apply the default algorithm
         */
        if (null === $shippingAddress) {
            if (true === SessionUser::isConnected()) {
                $userId = SessionUser::getValue('id');
                $shippingAddress = EkomApi::inst()->userAddressLayer()->getDefaultShippingAddress($userId);
                if (false === $shippingAddress) { // the user is connected but doesn't have a shipping address yet
                    $shippingAddress = null;
                }
            }


            if (null === $shippingAddress) { // if we cannot get the address from the user, we try the shop address
                $shippingAddress = $shopAddress;
            }


            if (null !== $shippingAddress) { // the shipping address is set, we can estimate the shipping costs

                /**
                 * We have to deal with the carriers choices that the shop has made
                 */

                if (true === EkomApi::inst()->carrierLayer()->useSingleCarrier()) {
                    if (false !== ($carrierInfo = $this->getSingleCarrier())) {
                        list($carrierName, $carrier) = $carrierInfo;
                        $carriers[$carrierName] = $carrier;
                    }
                } else { // the user has the choice, so we will take all shop available carriers to do our estimate
                    EkomApi::inst()->initWebContext();
                    $shopId = ApplicationRegistry::get("ekom.shop_id");
                    $carriers = $this->getCarrierInstancesByShop($shopId);
                }


            }


        }


        //--------------------------------------------
        // CALCULATING THE COSTS
        //--------------------------------------------
        return $this->calculateShippingCostsByCarriers($carriers, $productInfos, $shopAddress, $shippingAddress);

    }


    /**
     *
     * Calculate the shipping costs for an order with checkoutMode=singleAddress
     *
     * @param array $productInfos : same as estimateShippingCosts
     * @return false|array, same as estimateShippingCosts
     */
    public function calculateShippingCostByCarrierName($carrierName, array $productInfos, array $shippingAddress, $forReal = true)
    {


        $shopAddress = EkomApi::inst()->shopLayer()->getShopPhysicalAddress();
        if (false === $shopAddress) {
            $msg = "[Ekom module] - CarrierLayer.estimateShippingCosts: ekom config error, shop address is not defined";
            XLog::error($msg);
            $shopAddress = null;
        }


        /**
         * We have to deal with the carriers choices that the shop has made
         */
        EkomApi::inst()->initWebContext();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $rows = $this->getCarriersByShop($shopId);

        /**
         * @var $coll CarrierCollection
         */
        $coll = X::get("Ekom_getCarrierCollection");
        $carriers = [];
        if (false !== array_search($carrierName, $rows)) {
            if (false !== ($instance = $coll->getCarrier($carrierName))) {
                $carriers[$carrierName] = $instance;
                return $this->calculateShippingCostsByCarriers($carriers, $productInfos, $shopAddress, $shippingAddress, $forReal);
            } else {
                $msg = "[Ekom module] - CarrierLayer.calculateShippingCostByCarrierId, cannot find carrier instance with name: $carrierName";
                XLog::error($msg);
                throw new EkomApiException($msg);
            }
        } else {
            $msg = "[Ekom module] - CarrierLayer.calculateShippingCostByCarrierId, carrier not found with name $carrierName";
            XLog::error($msg);
            throw new EkomApiException($msg);
        }
    }


    /**
     * @param $shopId
     * @return array of carrier id => name
     */
    public function getCarriersByShop($shopId)
    {
        $shopId = (int)$shopId;
        return A::cache()->get("Ekom.CarrierLayer.getCarriersByShop.$shopId", function () use ($shopId) {
            return QuickPdo::fetchAll("
select c.id, c.name 
        
from ek_carrier c 
inner join ek_shop_has_carrier h on h.carrier_id=c.id
 
where h.shop_id=$shopId         

order by h.priority asc        
        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);

        }, [
            "ek_carrier.delete",
            "ek_carrier.update",
            "ek_shop_has_carrier.delete.$shopId",
            "ek_shop_has_carrier.update.$shopId",
            "ek_shop.delete.$shopId",
        ]);
    }


    /**
     * @param $shopId
     * @return array CarrierInterface[] (name => instance)
     */
    public function getCarrierInstancesByShop($shopId)
    {
        $rows = $this->getCarriersByShop($shopId);
        $ret = [];
        /**
         * @var $coll CarrierCollection
         */
        $coll = X::get("Ekom_getCarrierCollection");
        foreach ($rows as $id => $name) {
            if (false !== ($instance = $coll->getCarrier($name))) {
                $ret[$name] = $instance;
            } else {
                XLog::error("[Ekom module] - CarrierLayer: carrier $name found in database, but not as a class in the fileSystem");
            }
        }
        return $ret;
    }

    /**
     *
     * This method is used during the checkout process, where you want to display
     * a list of all available carriers to the user, along with some useful information such as
     * the estimated delivery date and the price of the order
     *
     * @param $productInfos, same as estimateShippingCosts
     * @return array of carrierName => carrierInfo
     *          - carrierInfo: array:
     *              - name: the carrierName
     *              - shippingCost:
     *              - rawShippingCost:
     *              - estimatedDeliveryDate:
     *              - carrierLabel:
     *              - productsInfo: handled products info **
     *              - notHandled:  not handled products info **
     *              - trackingNumber:
     *
     *
     *              ** product info depends on the given productInfos argument.
     *
     *
     *
     */
    public function getAllCarriersShippingCost(array $productInfos, array $shippingAddress)
    {

        $shopId = E::getShopId();
        $carriers = $this->getCarrierInstancesByShop($shopId);


        $shopAddress = EkomApi::inst()->shopLayer()->getShopPhysicalAddress();
        if (false === $shopAddress) {
            $msg = "[Ekom module] - CarrierLayer.getAllCarriersShippingCost: ekom config error, shop address is not defined";
            XLog::error($msg);
            $shopAddress = null;
        }


        $sections = [];
        foreach ($carriers as $name => $carrier) {
            /**
             * @var $carrier CarrierInterface
             */
            $handledProductsInfo = [];
            $notHandled = [];
            $rejected = [];
            $info = $carrier->handleOrder([
                'forReal' => false, // this is just an estimation
                'products' => $productInfos,
                'shopAddress' => $shopAddress,
                'shippingAddress' => $shippingAddress,
            ], $rejected);
            $shippingCost = $info["shipping_cost"];


            $trackingNumber = "";
            if (array_key_exists("tracking_number", $info)) {
                $trackingNumber = $info['tracking_number'];
            }

            $estimatedDeliveryDate = (array_key_exists('estimated_delivery_date', $info)) ? $info['estimated_delivery_date'] : null;

            foreach ($productInfos as $id => $info) {
                if (true === in_array($id, $rejected, true)) {
                    $notHandled[$id] = $info;
                } else {
                    $handledProductsInfo[$id] = $info;
                }
            }


            $sections[$name] = [
                'name' => $name,
                'shippingCost' => E::price($shippingCost),
                'rawShippingCost' => $shippingCost,
                'estimatedDeliveryDate' => $estimatedDeliveryDate,
                "carrierLabel" => $carrier->getLabel(),
                'productsInfo' => $handledProductsInfo,
                'trackingNumber' => $trackingNumber,
                'notHandled' => $notHandled,
            ];
        }
        return $sections;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return false|array [name, CarrierInterface]
     */
    private function getSingleCarrier()
    {
        $carrierSel = E::conf("carrierSelectionMode");
        if ("manual" !== $carrierSel) {
            if ("fixed:" === substr($carrierSel, 0, 6)) {
                $carrierName = substr($carrierSel, 6);
                return [$carrierName, $this->getCarrierByName($carrierName)];
            } else {
                XLog::error("[Ekom module] - CarrierLayer.getSingleCarrier: carrierSelectionMode not handled yet");
                return false;
            }
        }
        return false;
    }


    private function getSingleCarrierId()
    {
        $carrierSel = E::conf("carrierSelectionMode");
        if ("manual" !== $carrierSel) {
            if ("fixed:" === substr($carrierSel, 0, 6)) {
                $carrierName = substr($carrierSel, 6);
                return $this->getCarrierIdByName($carrierName);
            } else {
                XLog::error("[Ekom module] - CarrierLayer.getSingleCarrierId: carrierSelectionMode not handled yet");
                return false;
            }
        }
        return false;
    }

    private function getCarrierInfoById($carrierId, $shopId)
    {
        $rows = $this->getCarriersByShop($shopId);

        /**
         * @var $coll CarrierCollection
         */
        $coll = X::get("Ekom_getCarrierCollection");
        if (array_key_exists($carrierId, $rows)) {
            $name = $rows[$carrierId];
            if (false !== ($instance = $coll->getCarrier($name))) {
                return [$name, $instance];
            } else {
                $msg = "[Ekom module] - CarrierLayer: carrier $name found in database, but not as a class in the fileSystem";
                XLog::error($msg);
                throw new EkomApiException($msg);
            }
        } else {
            $msg = "[Ekom module] - CarrierLayer: cannot find carrier with id $carrierId";
            XLog::error($msg);
            throw new EkomApiException($msg);
        }
    }


    private function calculateShippingCostsByCarriers(array $carriers, array $productInfos, $shopAddress, $shippingAddress, $forReal = true)
    {
        $sections = [];
        $totalShippingCost = 0;
        foreach ($carriers as $name => $carrier) {
            /**
             * @var $carrier CarrierInterface
             */
            $rejected = [];
            $info = $carrier->handleOrder([
                'forReal' => $forReal,
                'products' => $productInfos,
                'shopAddress' => $shopAddress,
                'shippingAddress' => $shippingAddress,
            ], $rejected);
            $totalShippingCost += $info["shipping_cost"];


            $trackingNumber = "";
            if (array_key_exists("tracking_number", $info)) {
                $trackingNumber = $info['tracking_number'];
            }

            $estimatedDeliveryDate = (array_key_exists('estimated_delivery_date', $info)) ? $info['estimated_delivery_date'] : null;

            $handledProductsInfo = [];
            foreach ($productInfos as $id => $info) {
                if (false === in_array($id, $rejected, true)) {
                    $handledProductsInfo[$id] = $info;
                    unset($productInfos[$id]);
                }
            }

            if (0 === count($handledProductsInfo)) {
                continue;
            }


            $sections[] = [
                'name' => $name,
                'shippingCost' => E::price($totalShippingCost),
                'rawShippingCost' => $totalShippingCost,
                'estimatedDeliveryDate' => $estimatedDeliveryDate,
                "carrierLabel" => $carrier->getLabel(),
                'productsInfo' => $handledProductsInfo,
                'trackingNumber' => $trackingNumber,
            ];
            if (0 === count($productInfos)) {
                break;
            }
        }

        /**
         * At this point, the productInfos array might be empty
         * (at least if the app is properly configured and can ship every product)
         */
        $notHandled = $productInfos;

        return [
            'sections' => $sections,
            'notHandled' => $notHandled,
            'totalShippingCost' => $totalShippingCost,
        ];
    }




    private function getCarrierNameById($id)
    {
        return A::cache()->get("Ekom.CarrierLayer.getCarrierNameById.$id", function () use ($id) {
            return EkomApi::inst()->carrier()->readColumn("name", [
                ["id", "=", $id],
            ]);
        }, [
            "ek_carrier.delete.$id",
            "ek_carrier.update.$id",
        ]);
    }
}
