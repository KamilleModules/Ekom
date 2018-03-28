<?php


namespace Module\Ekom\Model\Front;


use Bat\BDotTool;
use HybridList\HybridListControl\HybridListControlInterface;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\HybridList\HybridListFactory;
use Module\Ekom\Models\EkomModels;
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
    public static function create()
    {
        return new static();
    }


    /**
     * @param $categorySlug
     * @param array $categoryModel ,
     * @see EkomModels::categoryModel()
     * @return array
     * @throws \Exception
     * @throws \HybridList\Exception\HybridListException
     */
    public static function getModelByCategorySlug($categorySlug, array &$categoryModel = [])
    {

        $model = [];
        $pool = $_GET;


        if (null !== $categorySlug) {

            if (false !== ($info = CategoryLayer::getInfoBySlug($categorySlug))) {
                $categoryModel = $info;
                $categoryId = $info['id'];


                $return = [];
                $hybridList = HybridListFactory::getCategoryHybridList($categoryId, $pool, $return);

                $info = $hybridList->execute();
//                az(__FILE__, $sqlRequest->getSqlRequest());

                $dotKey2Control = $return['dotKey2Control'];
                $context = $return['context'];


                $model['bundle'] = [
                    'general' => $info,
                    'slice' => $hybridList->getControl('slice')->getModel(),
                    'sort' => $hybridList->getControl('sort')->getModel(),
                    'filters' => [
                        'attributes' => $hybridList->getControl('attributes')->getModel(),
                        'price' => $hybridList->getControl('price')->getModel(),
                        'discounts' => $hybridList->getControl('discounts')->getModel(),
                        'summary' => $hybridList->getControl('summary')->getModel(),
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