<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use ListModifier\Circle\ListModifierCircle;
use ListModifier\Util\RequestModifier2RowsGeneratorAdaptorUtil;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomDebug;
use Module\Ekom\Utils\ListModifiers;
use QuickPdo\QuickPdo;
use RowsGenerator\ArrayRowsGenerator;

class ProductCardLayer
{


    public static function getLabelByCardId(int $cardId)
    {
        return QuickPdo::fetch("select label from ek_product_card where id=$cardId", [], \PDO::FETCH_COLUMN);
    }

    public static function getItemsList(array $options = [])
    {
        $alphaSort = $options['alphaSort'] ?? false;
        $q = "select id, label from ek_product_card";
        if ($alphaSort) {
            $q .= " order by label asc";
        }
        return QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function setDefaultProduct($cardId, $productId)
    {
        $cardId = (int)$cardId;
        $productId = (int)$productId;
        QuickPdo::update("ek_product_card", [
            "product_id" => $productId,
        ], [
            ['id', '=', $cardId],
        ]);
    }

    /**
     * IF the card is bound to AT LEAST ONE product
     *      THEN Ensures that the card has exactly one REPRESENTATIVE product
     */
    public static function sanityRoutine($cardId)
    {
        $cardId = (int)$cardId;
        $representativeProductId = QuickPdo::fetch("select product_id from ek_product_card where id=$cardId", [], \PDO::FETCH_COLUMN);
        /**
         * Note that if you delete the default product, the db is designed (cascade) to set the ek_product_card.product_id to null.
         */
        if (null === $representativeProductId) {


            // do we still have at least one product for this card?
            $id = QuickPdo::fetch("select id 
from ek_product p
where product_card_id=$cardId
order by id asc
", [], \PDO::FETCH_COLUMN);
            if ($id) { // yes, so this will be the default product now..
                QuickPdo::update("ek_product_card", [
                    "product_id" => $id,
                ], [
                    ['id', '=', $cardId],
                ]);
            }
        }
    }

    /**
     * Concept of dummy record is explained here: config/morphic/Ekom/back/catalog/product-product.form.conf.php
     * @param $cardId
     * @return false|int
     */
//    public static function getDummyRecordIdByCardId($cardId)
//    {
//        $cardId = (int)$cardId;
//        return QuickPdo::fetch("select id from ek_product where product_card_id=$cardId and reference='_dummy_'", [], \PDO::FETCH_COLUMN);
//    }

    public static function getItems()
    {
        $ret = [];
        $rows = QuickPdo::fetchAll("
select 
cl.product_card_id, cl.label, cl.slug 
from ek_product_card_lang cl 
inner join ek_product p on p.product_card_id=cl.product_card_id
");
        foreach ($rows as $row) {
            $s = self::getRepresentationByLabelSlug($row['label'], $row['slug']);
            $ret[$row["product_card_id"]] = $row["product_card_id"] . ". " . $s;
        }
        return $ret;
    }


    public static function getRepresentationById($id)
    {
        $id = (int)$id;
        $s = null;
        if (false !== ($info = QuickPdo::fetch("select label, slug from ek_product_card
where id=$id
"))) {
            $s = self::getRepresentationByLabelSlug($info['label'], $info['slug']);
        }
        if (!$s) {
            $s = "(all empty)";
        }
        return $s;
    }


    public static function getProductCardIdByRef($ref)
    {
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

    public static function getProductCardIdsByCategoryId($categoryId)
    {
        return A::cache()->get("Ekom.ProductCardLayer.getProductCardIdsByCategoryId.$categoryId", function () use ($categoryId) {
            $catIds = CategoryLayer::getSelfAndChildrenIdsById($categoryId);
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


    /**
     * @deprecated
     */
    public static function getLabelById($id, $shopId = null, $langId = null, $default = "")
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


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getRepresentationByLabelSlug($label, $slug)
    {
        $s = null;
        if ('' !== trim($label)) {
            $s = $label;
        } elseif ($slug) {
            {
                $s = ':' . $slug;
            }
        }
        if (!$s) {
            $s = "(all empty)";
        }
        return $s;
    }

}