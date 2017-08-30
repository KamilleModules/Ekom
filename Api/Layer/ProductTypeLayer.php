<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
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


    public function getIdByName($name, $createIfNotExist = true, $shopId = null)
    {
        if (null === $shopId) {
            $shopId = E::getShopId();
        }
        $shopId = (int)$shopId;


        $id = QuickPdo::fetch("select id from ek_product_type
where name=:name and shop_id=$shopId", [
            "name" => $name,
        ], \PDO::FETCH_COLUMN);

        if (false !== $id) {
            return $id;
        }
        if (true === $createIfNotExist) {
            return EkomApi::inst()->productType()->create([
                "name" => $name,
                "shop_id" => $shopId,
            ]);
        }
        return false;
    }
}