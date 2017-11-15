<?php


namespace Module\Ekom\Model\Front;


use Core\Services\Hooks;
use HybridList\RequestGenerator\SqlRequestGenerator;
use HybridList\SqlRequest\SqlRequest;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\HybridList\CategoryHybridList;
use Module\Ekom\HybridList\HybridListControl\Filter\AttributesFilterHybridListControl;
use Module\Ekom\HybridList\HybridListControl\Filter\PriceFilterHybridListControl;
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


                $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($categoryId);
                $unfilteredBoxes = []; // required by some filters/sort HybridListControl
                foreach ($cardIds as $cardId) {
                    $unfilteredBoxes[$cardId] = ProductBoxLayer::getProductBoxByCardId($cardId);
                }
                $sIds = implode(', ', $cardIds);


                //--------------------------------------------
                // HYBRID LIST
                //--------------------------------------------
                $sqlRequest = SqlRequest::create();
                $hybridList = CategoryHybridList::create()
                    ->setListParameters($pool)
                    ->setRequestGenerator(SqlRequestGenerator::create()
                        ->setPdoFetchStyle(\PDO::FETCH_COLUMN)
                        ->setSqlRequest($sqlRequest
                            ->addField("distinct chpc.product_card_id")
                            ->setTable("ek_category_has_product_card chpc")
                            ->addJoin("
inner join ek_product p on p.product_card_id=chpc.product_card_id                    
                    ")
                            ->addWhere("
and chpc.product_card_id in ($sIds)                            
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
                $productSortControl = ProductSortHybridListControl::create()->prepareHybridList($hybridList, $context);
                $pageControl = PaginateSliceHybridListControl::create()
                    ->setNumberOfItemsPerPage(20)
                    ->prepareHybridList($hybridList, $context);

                // other modules
                Hooks::call("Ekom_CategoryModel_decorateHybridList", $hybridList, $context);


                $info = $hybridList->execute();
                $model['bundle'] = [
                    'general' => $info,
                    'slice' => $pageControl->getModel(),
                    'sort' => $productSortControl->getModel(),
                    'filters' => [
                        'attributes' => $attributesFilterControl->getModel(),
                        'price' => $priceFilterControl->getModel(),
                    ],
                ];
                $model['category_id'] = $categoryId;



            }
        }
//        az(__FILE__, $info);
        return $model;
    }

}