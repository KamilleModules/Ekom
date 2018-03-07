<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\QueryFilterBox\QueryFilterBox\AttributesQueryFilterBox;
use Module\Ekom\QueryFilterBox\QueryFilterBox\CategoryQueryFilterBox;
use Module\Ekom\QueryFilterBox\QueryFilterBox\DiscountQueryFilterBox;
use Module\Ekom\QueryFilterBox\QueryFilterBox\PriceQueryFilterBox;
use Module\Ekom\QueryFilterBox\QueryFilterBox\SummaryItemsQueryFilterBox;
use Module\Ekom\Utils\E;
use Module\Ekom\WidgetBrain\Front\CategorySummary;
use Module\EkomTrainingProducts\QueryFilterBox\QueryFilterBox\QueryFilterBoxHelper;
use Module\EkomUserProductHistory\Api\EkomUserProductHistoryApi;
use QueryFilterBox\ItemsGenerator\ItemsGenerator;
use QueryFilterBox\Query\Query;
use QueryFilterBox\QueryFilterBox\SortQueryFilterBox;
use QueryFilterBox\Util\Paginator\Paginator;


class CategoryControllerOld2 extends EkomFrontController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();

        $slug = Z::getUrlParam("slug");
        $isB2b = E::isB2b();
        $langId = E::getLangId();
        $shopId = E::getShopId();


        if (null !== $slug) {
            if (false !== ($catId = EkomApi::inst()->categoryLayer()->getIdBySlug($slug))) {


                ApplicationRegistry::set("ekom.categoryId", $catId);


                $priceFilterBox = PriceQueryFilterBox::create()->setCategoryId($catId);
                $attributesFilterBox = AttributesQueryFilterBox::create()->setCategoryId($catId);
                $discountFilterBox = DiscountQueryFilterBox::create()->setCategoryId($catId);
                $categoryFilterBox = CategoryQueryFilterBox::create()->setCategoryId($catId);


                $summaryFilterBox = SummaryItemsQueryFilterBox::create()
                    ->setCategoryId($catId)
                    ->addCollectable($priceFilterBox)
                    ->addCollectable($attributesFilterBox)
                    ->addCollectable($discountFilterBox);


                $paginator = Paginator::create();

                $sortPrice = (true === $isB2b) ? 'shp._sale_price_without_tax' : 'shp._sale_price_with_tax';
                $sortLabel = "COALESCE(NULLIF(shpl.label,''), NULLIF(shcl.label,''), NULLIF(pl.label,''), cl.label)";

                $sortFilterBox = SortQueryFilterBox::create()
                    ->setDefaults('p.id', 'asc')
                    ->setSorts([
                        'default' => 'p.id',
                        'label_asc' => [$sortLabel, 'asc'],
                        'label_desc' => [$sortLabel, 'desc'],
                        'price_asc' => [$sortPrice, 'asc'],
                        'price_desc' => [$sortPrice, 'desc'],
                        'popularity' => ["shp._popularity", 'desc'],
                    ]);


                $gen = ItemsGenerator::create()
                    ->setPaginator($paginator)
                    /**
                     * Filter boxes should be attached via hooks
                     */
                    ->setFilterBox("sort", $sortFilterBox)
                    ->setFilterBox("price", $priceFilterBox)
                    ->setFilterBox("attributes", $attributesFilterBox)
                    ->setFilterBox("discounts", $discountFilterBox)
                    ->setFilterBox("summaryItems", $summaryFilterBox)
                    ->setFilterBox("category", $categoryFilterBox)
                    //--------------------------------------------
                    //
                    //--------------------------------------------
                    ->setQuery(Query::create()
                        ->setCountString("distinct chc.product_card_id")
                        ->addSelect("
chc.product_card_id
")
                        ->setFrom("ek_category_has_product_card chc")
                        ->addJoin("
inner join ek_shop_has_product_card shc on shc.product_card_id=chc.product_card_id
inner join ek_shop_has_product_card_lang shcl on shcl.shop_id=shc.shop_id and shcl.product_card_id=shcl.product_card_id
inner join ek_product p on p.product_card_id=chc.product_card_id
inner join ek_product_card_lang cl on cl.product_card_id=chc.product_card_id
inner join ek_product_lang pl on pl.product_id=p.id
inner join ek_shop_has_product shp on shp.product_id=shc.product_id
inner join ek_shop_has_product_lang shpl on shpl.shop_id=shp.shop_id and shpl.product_id=shp.product_id    
    ")
                        ->addWhere("
shcl.lang_id=$langId        
and cl.lang_id=$langId        
and shpl.lang_id=$langId        
and shc.shop_id=$shopId        
and pl.lang_id=$langId
and shc.active=1    
and shp.active=1     
    ")
                        ->setGroupBy("chc.product_card_id")
                        ->saveState()


                    );


                $claws = $this->getClaws();
                Hooks::call("Ekom_CategoryController_decorateItemsGeneratorAndClaws", $gen, $claws, [
                    'categoryId' => $catId,
                    'shop_id' => $shopId,
                    'lang_id' => $langId,
                ]);


                //--------------------------------------------
                // PROCESSING DATA
                //--------------------------------------------
                $pool = $_GET;
                $pool['category'] = $catId;
                $items = $gen->getItems($pool, \PDO::FETCH_COLUMN);


                /**
                 * In case items yields no results, you might want to provide the user with
                 * "similar" results (if you don't like empty results).
                 */
                Hooks::call("Ekom_CategoryController_decorateClawsPostRequest", $claws, [
                    'categoryId' => $catId,
                    'shop_id' => $shopId,
                    'lang_id' => $langId,
                    'countItems' => count($items),
                    'generator' => $gen,
                ]);


                $player = EkomApi::inst()->productLayer();
                $cards = [];
                foreach ($items as $cardId) {
                    $cards[] = $player->getProductBoxModelByCardId($cardId, $shopId, $langId);
                }
                $bundle = QueryFilterBoxHelper::getProductCardListBundleByCardsAndItemsGenerator($cards, $gen);




                $claws
                    ->setLayout("sandwich_2c/product-list")
                    ->setWidget("listheader.categorySummary", ClawsWidget::create()
                        ->setTemplate("Ekom/CategorySummary/default")
                        ->setConf(CategorySummary::create()->getModel($catId))
                    )
                    ->setWidget("maincontent.productList", ClawsWidget::create()
                        ->setTemplate("Ekom/ProductList/ProductCardList/default")
                        ->setConf([
                            'listBundle' => $bundle,
                        ])
                    )
                    //
//                    ->setWidget("maincontent.twoBanners", ClawsWidget::create()
//                        ->setTemplate("Ekom/TwoBanners/prototype5")
//                    )
//                    ->setWidget("maincontent.banner", ClawsWidget::create()
//                        ->setTemplate("Ekom/Banner/prototype3")
//                    )
                    ->setWidget("sidebar.summaryFilters", ClawsWidget::create()
                        ->setTemplate("Ekom/ListFilter/Summary/leaderfit")
                        ->setConf($summaryFilterBox->getModel()), 'first'
                    )
                    ->setWidget("sidebar.priceFilter", ClawsWidget::create()
                        ->setTemplate("Ekom/ListFilter/Price/default")
                        ->setConf($priceFilterBox->getModel()), "after:sidebar.summaryFilters"
                    )
                    ->setWidget("sidebar.attributeFilters", ClawsWidget::create()
                        ->setTemplate("Ekom/ListFilter/Attributes/default")
                        ->setConf([
                            'filterBoxes' => $attributesFilterBox->getModel(),
                        ])
                    )
                    ->setWidget("sidebar.discountFilter", ClawsWidget::create()
                        ->setTemplate("Ekom/ListFilter/Discount/default")
                        ->setConf($discountFilterBox->getModel()),
                        "after:sidebar.priceFilter"
                    );


                $userId = E::getUserId(null);
                if ($userId) {
                    $historyItems = EkomUserProductHistoryApi::inst()->generalLayer()->getHistoryItemsSlice($userId);
                    $claws->setWidget("listfooter.userLastVisitedProducts", ClawsWidget::create()
                        ->setTemplate("Ekom/CarouselProducts/default")
                        ->setConf([
                            'title' => "DERNIERS PRODUITS CONSULTÉS",
                            'products' => $historyItems,
                        ])
                    );
                }

            }
        }
    }

}


