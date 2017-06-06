<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\X;
use Ekom\Carrier\MockCarrier;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Carrier\CarrierInterface;
use Module\Ekom\Carrier\Collection\CarrierCollection;
use Module\Ekom\Carrier\Collection\CarrierCollectionInterface;

class CarrierLayer
{

    /**
     *
     * Distribute an order between the different available carriers (usually one is enough).
     *
     *
     * This method is used on the cart page, where shipping costs can be either
     * estimated (if the user is not connected) or calculated based on the
     * user address if the user is connected and has a shipping address.
     *
     * NOTE: if the user is connected AND doesn't have a shipping address, that's an ekom logic error,
     * and an exception is thrown.
     *
     *
     * @param array $productInfos , array of productId => productInfo,
     * each productInfo has at least the following keys:
     *
     * - product_id: int, the product id
     * - weight: float, the product weight
     *
     * @return array with the following structure:
     *          - sections: array of carrierName => sectionInfo, each sectionInfo is an array with the following structure:
     *                  - shippingCost: formatted shipping cost
     *                  - productsInfo: an array of product_id => productInfo
     *                          Each productInfo has the same structure as the passed productInfo.
     *          - notHandled: the array of not handled productInfo (productId => productInfo)
     *          - isEstimate: bool, whether or not the costs are just an estimate or the real shipping costs
     *          - totalShippingCost: string, formatted amount of shipping cost, sum of all sections' shipping costs.
     *
     *
     *
     *
     *
     * @throws EkomApiException if the shop address or the shipping address couldn't be guessed
     *
     */
    public function getCarrierGroups(array $productInfos)
    {

        $coll = X::get("Ekom_getCarrierCollection");
        /**
         * @var $coll CarrierCollection
         */
        $coll->addCarrier('mock1', MockCarrier::create()->setReturnPrice(100)->setRejected([6, 7, 8]));
        $coll->addCarrier('mock2', MockCarrier::create()->setReturnPrice(50)->setRejected([6]));
//        $coll->addCarrier('mock3', MockCarrier::create()->setReturnPrice(67)->setRejected([]));
        $carriers = $coll->all();


        $isEstimate = true;
        $shippingAddress = null;
        if (true === SessionUser::isConnected()) {
            $userId = SessionUser::getValue('id');
            $shippingAddress = EkomApi::inst()->userLayer()->getPreferredShippingAddress($userId);
            if (false === $shippingAddress) {
                $msg = "[Ekom module] - CarrierLayer.getCarrierGroups: ekom logic error, could not find a shipping address the user is connected but doesn't have an active shipping address yet";
                XLog::error($msg);
                throw new EkomApiException($msg);
            }
            $isEstimate = false;
        }


        $shopAddress = EkomApi::inst()->shopLayer()->getShopPhysicalAddress();
        if (false === $shopAddress) {
            $msg = "[Ekom module] - CarrierLayer.getCarrierGroups: ekom logic error, shop address is not defined";
            XLog::error($msg);
            throw new EkomApiException($msg);
        }


        /**
         * Heuristics to define the default address for estimating shipping costs for a non connected user.
         * You might want to enhance those heuristics (for now, just use the shop address)
         */
        if (null === $shippingAddress) {
            $shippingAddress = $shopAddress;
        }


        //--------------------------------------------
        // DISTRIBUTING THE PRODUCTS
        //--------------------------------------------
        /**
         * The goal is to distribute the products between the minimum number of (and cheapest) carriers
         * (in other words, what's the best choice for the customer).
         *
         * Usually, one carrier takes care of all products, but if that's not the case,
         * we want to use multiple carriers (if that can handle the customer's order that is).
         *
         * It is assumed the carriers are ordered by interestingForTheUserFactor (i.e. probably cheapest) desc.
         *
         *
         */
        $sections = [];
        $totalShippingCost = 0;
        foreach ($carriers as $name => $carrier) {

            /**
             * @var $carrier CarrierInterface
             */
            $rejected = [];
            $shippingCost = $carrier->handleOrder($productInfos, $shopAddress, $shippingAddress, $rejected);
            $totalShippingCost += $shippingCost;


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

            $sections[$name] = [
                'shippingCost' => $shippingCost,
                'productsInfo' => $handledProductsInfo,
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
            'isEstimate' => $isEstimate,
            'totalShippingCost' => $totalShippingCost,
        ];
    }
}
