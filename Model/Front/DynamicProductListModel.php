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
        $pool = [
            'badge' => "pt20",
            'sort' => "wholesale_price_desc",
            'boris' => "tamere",
            'diametre' => "75_cm",
            'price' => "20-200",
            'pass all get RELEVANT TO THE LIST here (it has to be filtered, because its
            used in formTrails by controls...',
//        'sort' => "wholesale_price_asc",
        ];


        if (null !== $categorySlug) {
            if (false !== ($info = CategoryLayer::getInfoBySlug($categorySlug))) {

                $categoryId = $info['id'];
                $categoryName = $info['name'];
                ApplicationRegistry::set("ekom.categoryId", $info['id']);


                $shopId = E::getShopId();
                $langId = E::getLangId();


//                $attr = AttributeLayer::getAvailableAttributeByCategoryId($categoryId);
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

                // other modules
                Hooks::call("Ekom_CategoryModel_decorateHybridList", $hybridList, $context);


                $info = $hybridList->execute();
                $items = $info['items'];
                $model['listInfo'] = $info;
                $model['category_id'] = $categoryId;

//                foreach ($items as $item) {
//                    a($item['label'] . ":" . $item['priceSaleRaw']);
//                }
//                a($attributesFilterControl->getModel());



            }
        }
        return $model;
    }

}