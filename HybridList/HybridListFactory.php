<?php


namespace Module\Ekom\HybridList;

use Core\Services\Hooks;
use HybridList\HybridListInterface;
use HybridList\RequestGenerator\SqlRequestGenerator;
use HybridList\SqlRequest\SqlRequest;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\Entity\ProductBoxEntityUtil;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\HybridList\HybridListControl\Filter\AttributesFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Filter\DiscountFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Filter\PriceFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Filter\SummaryFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Slice\PaginateSliceHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Sort\ProductSortHybridListControl;
use Module\Ekom\Utils\E;


/**
 * The goal of this factory is provide quick access to HybridList.
 *
 * Having a HybridLists, we can execute it multiple times with different parameters.
 *
 * This will  don't need to trigger the getModel method,
 *
 * This was useful in ekom for the fallback products, where if the numberItems is 0,
 * we trigger the same HybridList (same hybrid controls attached to them) but providing
 * different parameters until we reach our desired number of items.
 *
 *
 * You trigger the HybridList different states by using the pool ONLY.
 *
 *
 *
 *
 */
class HybridListFactory
{


    /**
     * @return HybridListInterface
     *
     */
    public static function getCategoryHybridList($categoryId, array $pool, array &$return = null, $shopId = null)
    {

        $shopId = E::getShopId($shopId);

        ApplicationRegistry::set("ekom.categoryId", $categoryId);

        $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($categoryId);

        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext([
            'shop_id' => $shopId,
        ]);
        $unfilteredBoxes = []; // required by some filters/sort HybridListControl
        foreach ($cardIds as $cardId) {
            $box = ProductBoxLayer::getProductBoxByCardId($cardId, null, [], $gpc);
            if (!array_key_exists('errorCode', $box)) {
                $unfilteredBoxes[$cardId] = ProductBoxLayer::getProductBoxByCardId($cardId);
            }
        }
        $sIds = implode(', ', $cardIds);
        //--------------------------------------------
        // HYBRID LIST
        //--------------------------------------------
        $sSql = (count($cardIds) > 0) ? "and chpc.product_card_id in ($sIds)" : "and chpc.product_card_id = -1";
        $sqlRequest = SqlRequest::create();
        $hybridList = CategoryHybridList::create()
            ->setListParameters($pool)
            ->setRequestGenerator(SqlRequestGenerator::create()
                ->setSqlRequest($sqlRequest
                    ->addField("distinct chpc.product_card_id, p.id as product_id")
                    ->setTable("ek_category_has_product_card chpc")
                    ->addJoin("
inner join ek_category c on c.id=chpc.category_id                            
inner join ek_product p on p.product_card_id=chpc.product_card_id   
inner join ek_shop_has_product_card shpc on shpc.shop_id=c.shop_id and shpc.product_card_id=p.product_card_id
inner join ek_shop_has_product shp on shp.shop_id=c.shop_id and shp.product_id=p.id             
                    ")
                    ->addWhere("
$sSql
and c.shop_id=$shopId             
and shpc.active=1               
and shp.active=1               
                            ")
                ));


        // ekom baked
        $attributesFilterControl = AttributesFilterHybridListControl::create();
        $priceFilterControl = PriceFilterHybridListControl::create();
        $discountsFilterControl = DiscountFilterHybridListControl::create();
        $summaryFilterControl = SummaryFilterHybridListControl::create()
            ->addSummaryFilterAwareItem($attributesFilterControl)
            ->addSummaryFilterAwareItem($priceFilterControl)
            ->addSummaryFilterAwareItem($discountsFilterControl)
        ;


        $hybridList->addControl("attributes", $attributesFilterControl);
        $hybridList->addControl("price", $priceFilterControl);
        $hybridList->addControl("discounts", $discountsFilterControl);
        $hybridList->addControl("sort", ProductSortHybridListControl::create());
        $hybridList->addControl("slice", PaginateSliceHybridListControl::create()->setNumberOfItemsPerPage(50));
        $hybridList->addControl("summary", $summaryFilterControl);


        $context = [
            'category_id' => $categoryId,
            'unfilteredBoxes' => $unfilteredBoxes,
            'pool' => $pool,
            'summaryFilterControl' => $summaryFilterControl,
            'shop_id' => $shopId,
        ];
        $hybridList->setControlsContext($context);


        // other modules
        $dotKey2Control = [];
        Hooks::call("Ekom_CategoryModel_prepareModelWithHybridList", $dotKey2Control, $hybridList, $context);


        if (null === $return) {
            $return = [];
        }
        $return['dotKey2Control'] = $dotKey2Control;
        $return['context'] = $context;

        return $hybridList;
    }
}