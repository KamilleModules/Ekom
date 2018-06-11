<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Carrier\CarrierInterface;
use Module\Ekom\Carrier\Collection\CarrierCollection;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 *
 *
 * currentCarrierModel
 * ----------------------
 * - id: int, carrier id
 * - name: string, carrier name
 *
 *
 */
class CarrierLayer
{
    private static $_carriers = null;


    /**
     * @param $shopId
     * @return CarrierInterface[] (id => instance)
     * @throws \Exception
     */
    public static function getCarrierInstances()
    {
        $rows = self::getCarriers();
        $ret = [];
        /**
         * @var $coll CarrierCollection
         */
        $coll = X::get("Ekom_getCarrierCollection");
        foreach ($rows as $id => $name) {
            if (false !== ($instance = $coll->getCarrier($name))) {
                $instance->setId($id);
                $instance->setName($name);
                $ret[$id] = $instance;
            } else {
                XLog::error("[Ekom module] - CarrierLayer: carrier $name found in database, but not as a class in the fileSystem");
            }
        }
        return $ret;
    }


    /**
     * @return CarrierInterface
     */
    public static function getCarrierInstanceById($carrierId)
    {
        $carrierId = (int)$carrierId;
        $rows = self::getCarriers();
        /**
         * If the user SELECTED (in the checkout) a carrier, use this carrier
         */
        if (array_key_exists($carrierId, $rows)) {
            $carrierName = $rows[$carrierId];
            $ret = self::getCarrierInstanceByName($carrierName);
            $ret->setId($carrierId);
            $ret->setName($carrierName);
            return $ret;
        }
        throw new \Exception("Carrier instance not found with id: $carrierId");
    }


    /**
     * @return array of carrier id => name
     */
    public static function getCarriers()
    {
        if (null === self::$_carriers) {
            self::$_carriers = A::cache()->get("Ekom.CarrierLayer.getCarriers", function () {
                return QuickPdo::fetchAll("
select c.id, c.name 
        
from ek_carrier c 
order by c.priority asc        
        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);

            }, 'ek_carrier');
        }
        return self::$_carriers;
    }


    public static function getAllCarriers()
    {

        return QuickPdo::fetchAll("
select id, `name` 
from ek_carrier 
order by `name` asc        
        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getShopDefaultCarrierId()
    {
        $carriers = self::getCarriers();
        if ($carriers) {
            foreach ($carriers as $id => $name) {
                return $id;
            }
        }
        throw new EkomException("This shop doesn't have any carrier yet");
    }

    /**
     *
     * @deprecated
     *
     * Get the shipping cost of the $carrier for a given context.
     *
     *
     * @param array $context , array containing different data:
     *              - boxes: array of productBoxModel
     *              - ?carrier: string|null, a carrier name to use.
     *                             If carrier is null, a default carrier will be chosen
     *
     *
     * @param array $details
     *          Details contains the following:
     *          - label: the carrier label
     *          - ?notHandled: array of productBoxModel item which cannot be handled by this carrier
     *          - ?estimated_delivery_date: the estimated delivery date provided by the carrier
     * @return number: the raw shipping cost without tax
     */
//    public static function getShippingCost(array $context = [], array &$details = [])
//    {
//        $context = array_replace([
//            'boxes' => [],
//            'carrier' => null,
//        ], $context);
//
//        $carrier = $context['carrier'];
//        $boxes = $context['boxes'];
//
//
//        if (null === $carrier) {
//            $carrier = self::getCurrentCarrier();
//        }
//
//        $userShippingAddress = UserAddressLayer::getCurrentShippingAddress(); // array|false
//
//
//        $shopAddress = self::getShopPhysicalAddressForShipping([
//            'userShippingAddress' => $userShippingAddress,
//        ]); // array|null
//
//        //--------------------------------------------
//        // CALCULATING THE COSTS
//        //--------------------------------------------
//        $carrierInstance = self::getCarrierInstanceByName($carrier);
//        $carrierLabel = $carrierInstance->getLabel();
//        $rejected = [];
//        $shippingInfo = $carrierInstance->getShippingCost([
//            'products' => $boxes,
//            'shopAddress' => $shopAddress,
//            'shippingAddress' => $userShippingAddress,
//        ], $rejected);
//        if ($rejected) {
//            $details['notHandled'] = $rejected;
//        }
//        if (array_key_exists("estimated_delivery_date", $shippingInfo)) {
//            $details['estimated_delivery_date'] = $shippingInfo['estimated_delivery_date'];
//        }
//        $details['label'] = $carrierLabel;
//        return E::trimPrice($shippingInfo['shipping_cost']);
//    }


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

        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $rows = self::getCarriers($shopId);

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
     *
     * This method is used during the checkout process, where you want to display
     * a list of all available carriers to the user, along with some useful information such as
     * the estimated delivery date and the price of the order
     *
     * @param $productInfos , same as estimateShippingCosts
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
        $carriers = $this->getCarrierInstances($shopId);
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
            $info = $carrier->getShippingCost([
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
     * @param $carrierName
     * @return null|CarrierInterface
     *
     *
     * @dev: do not set this public, because a CarrierInstance has a getId method, which should always provide
     * a real value (not 0).
     */
    private static function getCarrierInstanceByName($carrierName)
    {
        /**
         * @var $coll CarrierCollection
         */
        $coll = X::get("Ekom_getCarrierCollection");
        if (false !== ($instance = $coll->getCarrier($carrierName))) {
            return $instance;
        } else {
            XLog::error("[Ekom module] - CarrierLayer: carrier $carrierName found in database, but not as a class in the fileSystem");
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


}
