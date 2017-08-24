<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class ProductSelectionLayer
{


    public function getProductBoxModelsByGroup($productGroupName, $shopId = null)
    {
        if (null === $shopId) {
            $shopId = E::getShopId();
        }
        $shopId = (int)$shopId;

        return A::cache()->get("ProductSelectionLayer.getProductBoxModelsByGroup.$shopId.$productGroupName", function () use ($productGroupName, $shopId) {
            $ids = EkomApi::inst()->productGroupLayer()->getProductIdsByGroup($productGroupName, $shopId);
            $ret = [];
            $pLayer = EkomApi::inst()->productLayer();
            foreach ($ids as $id) {
                $ret[] = $pLayer->getProductBoxModelByProductId($id, $shopId);
            }
            return $ret;
        }, [
            // ProductGroupLayer.getProductIdsByGroup
            'ek_product_group_has_product',
            'ek_product_group',
        ]);
    }

}



