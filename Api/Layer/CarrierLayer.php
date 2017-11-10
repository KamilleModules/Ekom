<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\Hooks;
use Core\Services\X;
use Ekom\Carrier\MockCarrier;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Carrier\CarrierInterface;
use Module\Ekom\Carrier\Collection\CarrierCollection;
use Module\Ekom\Carrier\Collection\CarrierCollectionInterface;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 * Carrier vs CarrierInstance
 * ===============================
 * In this class,
 * CarrierInstance represents an instance of CarrierInterface,
 * while Carrier is just the carrier name (i.e. a string).
 *
 */
class CarrierLayer
{

    /**
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
    public static function getShippingCost(array $context = [], array &$details = [])
    {
        $context = array_replace([
            'boxes' => [],
            'carrier' => null,
        ], $context);

        $carrier = $context['carrier'];
        $boxes = $context['boxes'];

        if (null === $carrier) {
            $carrier = self::getCurrentCarrier();
        }


        $userShippingAddress = UserAddressLayer::getCurrentShippingAddress(); // array|null
        $shopAddress = self::getShopPhysicalAddressForShipping([
            'userShippingAddress' => $userShippingAddress,
        ]); // array|null

        //--------------------------------------------
        // CALCULATING THE COSTS
        //--------------------------------------------
        $carrierInstance = self::getCarrierInstanceByName($carrier);
        $carrierLabel = $carrierInstance->getLabel();
        $rejected = [];
        $shippingInfo = $carrierInstance->getShippingCost([
            'products' => $boxes,
            'shopAddress' => $shopAddress,
            'shippingAddress' => $userShippingAddress,
        ], $rejected);
        if ($rejected) {
            $details['notHandled'] = $rejected;
        }
        if (array_key_exists("estimated_delivery_date", $shippingInfo)) {
            $details['estimated_delivery_date'] = $shippingInfo['estimated_delivery_date'];
        }
        $details['label'] = $carrierLabel;
        return E::trimPrice($shippingInfo['shipping_cost']);
    }


    /**
     * @return string, the name of the current carrier
     */
    public static function getCurrentCarrier()
    {
        if (true === CurrentCheckoutData::isStarted()) {
            return CurrentCheckoutData::get('carrier');
        }
        return self::getDefaultCarrier();
    }

    public static function getDefaultCarrier($shopId = null)
    {
        $carriers = self::getCarriers($shopId);
        if ($carriers) {
            return array_shift($carriers);
        }
        throw new EkomException("No carrier is set for shop with id $shopId");
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





    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param $shopId
     * @return array of carrier id => name
     */
    private static function getCarriers($shopId = null)
    {
        $shopId = E::getShopId($shopId);
        return A::cache()->get("Ekom.CarrierLayer.getCarriers.$shopId", function () use ($shopId) {
            return QuickPdo::fetchAll("
select c.id, c.name 
        
from ek_carrier c 
inner join ek_shop_has_carrier h on h.carrier_id=c.id
 
where h.shop_id=$shopId         

order by h.priority asc        
        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);

        }, [
            "ek_carrier",
            "ek_shop_has_carrier",
            "ek_shop",
        ]);
    }


    /**
     * @return false|int
     */
    private static function getCarrierIdByName($name, $shopId = null)
    {

        $carriers = self::getCarriers($shopId);
        foreach ($carriers as $id => $carrierName) {
            if ($carrierName === $name) {
                return (int)$id;
            }
        }
        return false;
    }


    private static function getShopPhysicalAddressForShipping(array $data)
    {
        /**
         * Here we decide the physical address of the shop which might be involved
         * in shipping cost calculation.
         */
        $shopAddress = [];
        Hooks::call("Ekom_Carrier_getShopPhysicalAddressForShipping", $shopAddress, $data);
        if (empty($shopAddress)) {
            $shopAddress = ShopLayer::getClosestPhysicalAddress($data['userShippingAddress']);
        }
        return $shopAddress;
    }


    /**
     * @param $shopId
     * @return array CarrierInterface[] (name => instance)
     */
    private static function getCarrierInstancesByShop($shopId = null)
    {
        $rows = self::getCarriers($shopId);
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
     * @return CarrierInterface
     * @throws EkomApiException
     */
    private static function getCarrierInstanceByName($name, $shopId = null)
    {
        $instances = self::getCarrierInstancesByShop($shopId);
        if (array_key_exists($name, $instances)) {
            return $instances[$name];
        }
        throw new EkomApiException("carrier instance not found with name $name in shop $shopId");
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
