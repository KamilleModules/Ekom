<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use ListModifier\Circle\ListModifierCircle;
use ListModifier\Util\RequestModifier2RowsGeneratorAdaptorUtil;
use Module\Ekom\Api\EkomApi;
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






    public function getProductCardsByCategory($categoryId, $isB2b, ListModifierCircle $circle = null, $shopId = null, $langId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? (int)ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? (int)ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $categoryId = (int)$categoryId;
        $catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($categoryId);


        return A::cache()->get("Ekom.ProductCardLayer.getProductCardsByCategory.$shopId.$langId.$categoryId.$circle", function () use ($circle, $catIds, $langId, $shopId) {


//            $rows = QuickPdo::fetchAll("
//select chc.product_card_id
//
//from ek_category_has_product_card chc
//
//inner join ek_shop_has_product_card shc on shc.product_card_id=chc.product_card_id
//where chc.category_id in(" . implode(', ', $catIds) . ")
//and shc.shop_id=$shopId
//and shc.active=1
//
//
//
//        ");


            $sJoin = "";
            $sWhere = "";
            $sOrder = "";
            $markers = [];


            $mod = $circle->getRequestModifier();
            $searchItems = $mod->getSearchItems();
            $sortItems = $mod->getSortItems();

            $c = 0;
            foreach ($sortItems as $sortItem => $dir) {
                if (0 !== $c++) {
                    $sOrder .= ", ";
                }
                if ('label' === $sortItem) {
                    $sortItem = "
COALESCE(NULLIF(shpl.label,''), NULLIF(shcl.label,''), NULLIF(pl.label,''), cl.label)            
                    ";
                    $sOrder .= "$sortItem $dir";
                } else {
                    $sOrder .= "$sortItem $dir";
                }
            }


            $included = false;
            $c = 0;
            foreach ($searchItems as $name => $searchItem) {
                // assuming operator is safe
                list($operand, $operator, $operand2) = $searchItem;

                switch ($operator) {
                    case 'between':

                        if ('price' === $name) {


                            $tagMin = "attrmin" . $c;
                            $tagMax = "attrmax" . $c;
                            $c++;

                            $sWhere .= "
and shp._sale_price_without_tax >= :$tagMin                
and shp._sale_price_without_tax <= :$tagMax                
                ";
                            $markers[$tagMin] = $operand;
                            $markers[$tagMax] = $operand2;
                        }

                        break;
                    case 'in': // attributes
                        $safeIds = $operand;
                        $tagName = "attrname" . $c;
                        $c++;

                        $sWhere .= "
and a.name = :$tagName
and v.id in (" . implode(', ', $safeIds) . ")                
                ";


                        $markers[$tagName] = $name;

                        break;
                    default:
                        $tagName = "attrname" . $c;
                        $tagValue = "attrvalue" . $c;
                        $c++;

                        $sWhere .= "
and a.name = :$tagName
and v.value $operator :$tagValue                
                ";
                        $markers[$tagName] = $name;
                        $markers[$tagValue] = $operand;
                        break;
                }

                if ('price' !== $name) {
                    if (false === $included) {

                        $included = true;
                        $sJoin .= "
inner join ek_product_has_product_attribute h on h.product_id=p.id
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id  
                ";

                    }
                }

            }


            $sTheOrder = "";
            if (!empty($sOrder)) {
                $sTheOrder = "order by " . $sOrder;
            }

            $query = "
select 

chc.product_card_id,
p.id,
shcl.label as cardLabel,
cl.label as cardLabelDefault,
shpl.label as label,
pl.label as labelDefault


from ek_category_has_product_card chc
  
inner join ek_shop_has_product_card shc on shc.product_card_id=chc.product_card_id
inner join ek_shop_has_product_card_lang shcl on shcl.shop_id=shc.shop_id and shcl.product_card_id=shcl.product_card_id
inner join ek_product p on p.product_card_id=chc.product_card_id
inner join ek_product_card_lang cl on cl.product_card_id=chc.product_card_id
inner join ek_product_lang pl on pl.product_id=p.id
inner join ek_shop_has_product shp on shp.product_id=shc.product_id
inner join ek_shop_has_product_lang shpl on shpl.shop_id=shp.shop_id and shpl.product_id=shp.product_id



$sJoin

where chc.category_id in(" . implode(', ', $catIds) . ")
and shcl.lang_id=$langId        
and cl.lang_id=$langId        
and shpl.lang_id=$langId        
and shc.shop_id=$shopId        
and pl.lang_id=$langId
and shc.active=1    
and shp.active=1    

$sWhere
        
        
        
group by chc.product_card_id


$sTheOrder
        
        ";

            EkomDebug::$debug = $query;


            $rows = QuickPdo::fetchAll($query, $markers);
//            a(count($rows));
//            az($query);

            /**
             * Todo: if bad perfs: try to compile all necessary info in the sql query above
             */
            $ret = [];
            $productLayer = EkomApi::inst()->productLayer();
            foreach ($rows as $row) {
                $ret[] = $productLayer->getProductBoxModelByCardId($row['product_card_id'], $shopId, $langId);
            }

//            az($catIds, $rows);


//            $gen = ArrayRowsGenerator::create()->setArray($ret);
//            RequestModifier2RowsGeneratorAdaptorUtil::decorate($gen, $circle);
//            $ret = $gen->getRows();

            return $ret;

        }, [
            "ek_category_has_product_card",
            "ek_product_card",
            "ek_product_card_lang",
            "ek_product",
            "ek_product_lang",
            "ek_product_has_product_attribute",
            "ek_product_attribute",
            "ek_product_attribute_value",
            "ek_shop_has_product",
            "ek_shop_has_product_lang",
            "ek_shop_has_product_card_lang",
            "ek_shop_has_product_card.create",
            "ek_shop_has_product_card.delete.$shopId",
            "ek_shop_has_product_card.update.$shopId",
        ]);
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

}