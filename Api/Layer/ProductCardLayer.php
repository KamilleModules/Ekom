<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use ListModifier\Circle\ListModifierCircle;
use ListModifier\Util\RequestModifier2RowsGeneratorAdaptorUtil;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomDebug;
use Module\Ekom\Utils\ListModifiers;
use QuickPdo\QuickPdo;
use RowsGenerator\ArrayRowsGenerator;

class ProductCardLayer
{


    public static function getProductCardIdByRef($ref){
        return QuickPdo::fetch("select product_card_id from ek_product where reference=:ref", [
            'ref' => $ref,
        ], \PDO::FETCH_COLUMN);
    }

    public static function getProductIds($cardId)
    {
        $cardId = (int)$cardId;
        return QuickPdo::fetchAll("
select id from ek_product where product_card_id=$cardId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getProductCardIdsByCategoryId($categoryId, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        return A::cache()->get("Ekom.ProductCardLayer.getProductCardIdsByCategoryId.$shopId.$categoryId", function () use ($categoryId, $shopId) {
            $catIds = CategoryLayer::getSelfAndChildrenIdsById($categoryId, $shopId);
            return self::getProductCardIdsByCategoryIds($catIds);

        });
    }


    public static function getProductCardIdsByCategoryIds(array $categoryIds)
    {
        $sCatIds = '"' . implode('", "', $categoryIds) . '"';
        $ret = QuickPdo::fetchAll("
select product_card_id from ek_category_has_product_card 
where category_id in ($sCatIds)
", [], \PDO::FETCH_COLUMN);
        $ret = array_unique($ret);
        sort($ret);
        return $ret;
    }


    public static function getProductId2CardIdByProductIds(array $productIds)
    {
        if ($productIds) {
            $productIds = array_unique($productIds);
            $sIds = implode(', ', array_map('intval', $productIds));
            return QuickPdo::fetchAll("
select id, product_card_id from ek_product where id in ($sIds)        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }
        return [];
    }


    public static function getIdByProductId($productId)
    {
        $productId = (int)$productId;
        return QuickPdo::fetch("select product_card_id from ek_product where id=$productId", [], \PDO::FETCH_COLUMN);
    }


    public function getProductCardIdsByProductIds(array $productIds)
    {
        if ($productIds) {

            $sIds = implode(', ', array_map('intval', $productIds));
            return QuickPdo::fetchAll("
select DISTINCT product_card_id from ek_product where id in ($sIds)        
        ", [], \PDO::FETCH_COLUMN);
        }
        return [];
    }

    public function getLabelById($id, $shopId = null, $langId = null, $default = "")
    {

        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);
        if (false !== ($label = QuickPdo::fetch("
select 
COALESCE( NULLIF(shpcl.label, ''), pcl.label ) as label
from 
ek_product_card_lang pcl 
inner join 
ek_shop_has_product_card_lang shpcl on shpcl.product_card_id=pcl.product_card_id and shpcl.lang_id=pcl.lang_id
where 
pcl.lang_id=$langId
and shpcl.shop_id=$shopId 
and pcl.product_card_id=$id        
        ", [], \PDO::FETCH_COLUMN))) {
            return $label;
        }
        return $default;
    }


    /**
     * A maintenance method to do "batch" operations on cards.
     */
    public function getProductCardIdsByShop($shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = E::getShopId($shopId);

        return QuickPdo::fetchAll("
select product_card_id from ek_shop_has_product_card
where shop_id=$shopId        
        ", [], \PDO::FETCH_COLUMN);
    }


    public function getProductCardInfosByCategoryIds(array $categoryIds, $langId = null)
    {
        $langId = E::getLangId($langId);
        $sCatIds = '"' . implode('", "', $categoryIds) . '"';

        $q = "
select
cat.name as category_name, 
h.product_card_id,
shpc.product_id,
shpc.active,
COALESCE(NULLIF(l.label,''), cl.label) as label,
COALESCE(NULLIF(l.slug,''), cl.slug) as slug,
shp._sale_price_without_tax,
shp._sale_price_with_tax,
COALESCE(shp.price, p.price) as price


from ek_category cat 
inner join ek_category_has_product_card h on h.category_id=cat.id
inner join ek_shop_has_product_card shpc on shpc.product_card_id=h.product_card_id and cat.shop_id=shpc.shop_id
inner join ek_shop_has_product_card_lang l on l.shop_id=cat.shop_id and l.product_card_id=shpc.product_card_id and l.lang_id=$langId
inner join ek_product_card_lang cl on cl.product_card_id=h.product_card_id and cl.lang_id=l.lang_id
inner join ek_shop_has_product shp on shp.shop_id=l.shop_id and shp.product_id=shpc.product_id
inner join ek_product p on p.id=shp.product_id

 
where h.category_id in ($sCatIds)
and l.lang_id=$langId        
        ";

        return QuickPdo::fetchAll($q);

    }


    public function setTaxGroup($cardId, $taxGroupId, $shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? (int)ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        EkomApi::inst()->productCardHasTaxGroup()->create([
            "shop_id" => $shopId,
            "product_card_id" => $cardId,
            "tax_group_id" => $taxGroupId,
        ]);
    }


    /**
     * Clean records in ek_product_card that have no correspondence in the ek_product_card_lang table.
     */
    public function cleanGhostRecords(array &$deletedIds = [])
    {
        $cLayer = EkomApi::inst()->productCard();
        $ids = QuickPdo::fetchAll("select id from ek_product_card", [], \PDO::FETCH_COLUMN);
        foreach ($ids as $id) {
            if (false === QuickPdo::fetch("select lang_id from ek_product_card_lang where product_card_id=$id")) {
                $cLayer->delete([
                    'id' => $id,
                ]);
                $deletedIds[] = $id;
            }
        }
    }

}