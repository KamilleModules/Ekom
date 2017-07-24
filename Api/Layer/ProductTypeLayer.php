<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class ProductTypeLayer
{

    public function registerNewType($type)
    {
        $row = QuickPdo::fetch("select id from ek_product_type
where name=:name", [
            "name" => $type,
        ]);

        if (false === $row) {
            $productType = EkomApi::inst()->productType();
            $shopIds = EkomApi::inst()->shopLayer()->allIds();
            foreach ($shopIds as $shopId) {
                $productType->create([
                    "name" => $type,
                    "shop_id" => $shopId,
                ]);
            }
        }
    }
}