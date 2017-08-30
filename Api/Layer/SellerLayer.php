<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class SellerLayer
{

    public function getProductIdsBySellerName($name, $shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;


        return QuickPdo::fetchAll("
select distinct
h.product_id
from 
ek_shop_has_product h
inner join ek_seller s on s.id=h.seller_id 
where s.name=:name 
and h.shop_id=$shopId
", [
            "name" => $name,
        ],
            \PDO::FETCH_COLUMN
        );
    }


    /**
     * @return int, the id of the seller
     */
    public function insertNameIfNotExist($name, $shopId = null)
    {
        if (null === $shopId) {
            $shopId = E::getShopId();
        }
        $shopId = (int)$shopId;

        $id = QuickPdo::fetch("select 
id 
from ek_seller 
where name = :name 
and shop_id=$shopId", [
            "name" => $name,
        ], \PDO::FETCH_COLUMN);
        if (false === $id) {
            $id = EkomApi::inst()->seller()->create([
                "name" => $name,
                "shop_id" => $shopId,
            ]);
        }
        return (int)$id;
    }

    public function getIdByName($name, $shopId = null)
    {
        return self::insertNameIfNotExist($name, $shopId);
    }

}