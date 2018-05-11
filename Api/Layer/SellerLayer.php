<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class SellerLayer
{

    public static function getSellerInfoByName(string $name)
    {
        return QuickPdo::fetch("select * from ek_seller where name=:name", [
            "name" => $name,
        ]);
    }


    public static function getItemsList(array $options = [])
    {
        $alphaSort = $options['alphaSort'] ?? false;
        $q = "select id, label from ek_seller";
        if ($alphaSort) {
            $q .= " order by label asc";
        }
        return QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getName2LabelList()
    {
        return QuickPdo::fetchAll("
select name, label from ek_seller        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getRowAvatar($sellerId)
    {
        return self::getNameById($sellerId);
    }


    public static function getItems($shopId)
    {
        return QuickPdo::fetchAll("
select id, name from ek_seller 
where shop_id=$shopId
order by name asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getIdByName($name)
    {
        $id = A::cache()->get("Ekom.SellerLayer.getIdByName.$name", function () use ($name) {
            return QuickPdo::fetch("select id from ek_seller where name=:name", ['name' => $name], \PDO::FETCH_COLUMN);
        });
        if (false === $id) {
            throw new EkomException("Seller not found with name $name");
        }
        return $id;
    }

    public static function getNameById($sellerId)
    {
        $sellerId = (int)$sellerId;
        return A::cache()->get("Ekom.SellerLayer.getNameById.$sellerId", function () use ($sellerId) {
            return QuickPdo::fetch("select name from ek_seller where id=$sellerId", [], \PDO::FETCH_COLUMN);
        });
    }


    /**
     *
     * Return the administrative address of the seller.
     *
     */
    public static function getSellerAddressByName(string $sellerName)
    {
        /**
         * Some sellers are just virtual and dynamically created.
         * This was the case for our company:
         * "formation-belgique" is a seller created from the seller "formation", only if the client's origin country
         * is Belgium (meaning the same product can be sold by two different sellers depending on the user's
         * origin country).
         *
         * If that's the case, as far as the seller address is concerned, virtual/dynamic sellers might want to
         * use their own heuristics here.
         * A tip is to insert the virtual seller name in the ek_seller table, but affecting no products to it.
         *
         */
        $sellerAddress = A::cache()->get("Ekom.SellerLayer.getSellerAddressByName", function () use ($sellerName) {

            $q = "
            select 
a.*,
c.iso_code as country_iso_code,
c.label as country

from ek_seller s 
inner join ek_address a on a.id=s.address_id 
inner join ek_country c on c.id=a.country_id  
where s.name=:seller
            ";


            return QuickPdo::fetch($q, ['seller' => $sellerName]);
        });
        if (false === $sellerAddress) {
            throw new EkomException("Seller $sellerName was not found or has no address");
        }
        return $sellerAddress;
    }


    public function getProductIdsBySellerName($name, $shopId = null)
    {

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
}