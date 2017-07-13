<?php


namespace Module\Ekom\Api\Util;


use Module\Ekom\Api\EkomApi;


class SellerUtil
{


    public static function removeAllProductsBySellerName($sellerName, $shopId = null)
    {

        $h = EkomApi::inst()->productHelperLayer();
        $ids = EkomApi::inst()->sellerLayer()->getProductIdsBySellerName($sellerName, $shopId);
        foreach ($ids as $id) {
            $h->removeProductById($id);
        }
    }

}