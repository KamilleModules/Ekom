<?php


namespace Module\Ekom\Model\Front;


use Bat\BdotTool;
use Core\Services\Hooks;
use HybridList\RequestGenerator\SqlRequestGenerator;
use HybridList\SqlRequest\SqlRequest;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\HybridList\CategoryHybridList;
use Module\Ekom\HybridList\HybridListControl\Filter\AttributesFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Filter\DiscountFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Filter\PriceFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Filter\SummaryFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\HybridListControlInterface;
use Module\Ekom\HybridList\HybridListControl\Slice\PaginateSliceHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Sort\ProductSortHybridListControl;
use Module\Ekom\Utils\E;


/**
 * Note: this DynamicProductListModel is based on the constraints in my company:
 * where the price depends on the user (origin country, shipping country, group, ...).
 *
 * Therefore, it's highly dynamical and very expensive in terms of performance.
 * We can't do it without a good caching strategy.
 *
 * Please if you need a less complex model create another model (like SimpleProductListModel for
 * instance) and redo your own logic.
 * Remember that controllers should be thin and used to branch models (like this one)
 * to the view.
 *
 *
 */
class DynamicProductListModel
{
    private $useAttribute;

    public function __construct()
    {
        $this->useAttribute = true;
    }

    public static function create()
    {
        return new static();
    }

    public function setUseAttribute($useAttribute)
    {
        $this->useAttribute = $useAttribute;
        return $this;
    }

    public function getModelBySlug($categorySlug)
    {

        $model = [];
        $pool = $_GET;
//        $pool = [
//            /**
//             * 'pass all get RELEVANT TO THE LIST here (it has to be filtered, because its
//             * used in formTrails by controls...',
//             *
//             * Note: the order here defines the order in which the shapers are called
//             */
//            'badge' => "pt20",
//            'price' => "20-200",
//            'sort' => "popularity",
//            'boris' => "tamere",
////            'diametre' => "75_cm",
////        'sort' => "wholesale_price_asc",
//        ];


        if (null !== $categorySlug) {
            if (false !== ($info = CategoryLayer::getInfoBySlug($categorySlug))) {

                $categoryId = $info['id'];
                ApplicationRegistry::set("ekom.categoryId", $info['id']);
                $shopId = E::getShopId();


                $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($categoryId);
                $unfilteredBoxes = []; // required by some filters/sort HybridListControl
                foreach ($cardIds as $cardId) {
                    $unfilteredBoxes[$cardId] = ProductBoxLayer::getProductBoxByCardId($cardId);
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


                $context = [
                    'category_id' => $categoryId,
                    'unfilteredBoxes' => $unfilteredBoxes,
                    'pool' => $pool,
                ];


                // ekom baked
                $attributesFilterControl = AttributesFilterHybridListControl::create()->prepareHybridList($hybridList, $context);
                $priceFilterControl = PriceFilterHybridListControl::create()->prepareHybridList($hybridList, $context);
                $discountFilterControl = DiscountFilterHybridListControl::create()->prepareHybridList($hybridList, $context);
                $productSortControl = ProductSortHybridListControl::create()->prepareHybridList($hybridList, $context);
                $pageControl = PaginateSliceHybridListControl::create()
                    ->setNumberOfItemsPerPage(50)
                    ->prepareHybridList($hybridList, $context);
                $summaryFilterControl = SummaryFilterHybridListControl::create()
                    ->addSummaryFilterAwareItem($attributesFilterControl)
                    ->addSummaryFilterAwareItem($priceFilterControl)
                    ->prepareHybridList($hybridList, $context);

                $context['summaryFilterControl'] = $summaryFilterControl;


                // other modules
                $dotKey2Control = [];
                Hooks::call("Ekom_CategoryModel_prepareModelWithHybridList", $dotKey2Control, $hybridList, $context);


                $info = $hybridList->execute();
//                az(__FILE__, $sqlRequest->getSqlRequest());


                $model['bundle'] = [
                    'general' => $info,
                    'slice' => $pageControl->getModel(),
                    'sort' => $productSortControl->getModel(),
                    'filters' => [
                        'attributes' => $attributesFilterControl->getModel(),
                        'price' => $priceFilterControl->getModel(),
                        'discounts' => $discountFilterControl->getModel(),
                        'summary' => $summaryFilterControl->getModel(),
                    ],
                ];
                foreach ($dotKey2Control as $dotKey => $control) {
                    /**
                     * @var $control HybridListControlInterface
                     */
                    BdotTool::setDotValue($dotKey, $control->getModel(), $model);
                }
                $model['category_id'] = $categoryId;
                $model['context'] = $context;


            }
        }
//        az(__FILE__, $info['items']);
        return $model;
    }

}