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


    /**
     * A maintenance method to do "batch" operations on cards.
     */
    public function getProductCardIdsByShop($shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? (int)ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;

        return QuickPdo::fetchAll("
select product_card_id from ek_shop_has_product_card
where shop_id=$shopId        
        ", [], \PDO::FETCH_COLUMN);
    }


    public function getProductCardIdsByCategoryIds(array $categoryIds)
    {
        $sCatIds = '"' . implode('", "', $categoryIds) . '"';
        return QuickPdo::fetchAll("
select product_card_id from ek_category_has_product_card 
where category_id in ($sCatIds)
", [], \PDO::FETCH_COLUMN);

    }

    public function getProductCardInfosByCategoryIds(array $categoryIds, $langId=null)
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