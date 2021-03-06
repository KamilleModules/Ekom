<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\EkomUserProductHistory\UserProductHistory\UserProductHistoryInterface;

/**
 * @deprecated use ProductBoxLayer instead
 * deprecation in progress...
 */
class ProductSelectionLayer
{


//    public function getProductBoxModelsByGroup($productGroupName, $shopId = null)
//    {
//        if (null === $shopId) {
//            $shopId = E::getShopId();
//        }
//        $shopId = (int)$shopId;
//
//        return A::cache()->get("Ekom.ProductSelectionLayer.getProductBoxModelsByGroup.$shopId.$productGroupName", function () use ($productGroupName, $shopId) {
//            $ids = EkomApi::inst()->productGroupLayer()->getProductIdsByGroup($productGroupName, $shopId);
//            return $this->getBoxesByIds($ids, $shopId);
//        }, [
//            // ProductGroupLayer.getProductIdsByGroup
//            'ek_product_group_has_product',
//            'ek_product_group',
//        ]);
//    }


    /**
     * @todo-ling: replace all getBoxesByIds methods with getBoxesByProductsInfo methods.
     *
     */
    public function getProductBoxModelsByAnyInCategoryAndUp($categoryName, $shopId = null)
    {
        return A::cache()->get("ProductSelectionLayer.getProductBoxModelsByAnyInCategoryAndUp.$shopId.$categoryName", function () use ($categoryName, $shopId) {

            $ids = [];
            EkomApi::inst()->categoryLayer()->collectProductIdsByCategoryName($ids, $categoryName, 10);
            return $this->getBoxesByIds($ids, $shopId);

        }, [
            'ek_product',
            'ek_category',
        ]);
    }

    public function getBoxesByIds(array $ids)
    {
        $ret = [];
        $pLayer = EkomApi::inst()->productLayer();
        foreach ($ids as $id) {
            $ret[] = $pLayer->getProductBoxModelByProductId($id);
        }
        return $ret;
    }

    /**
     * @param array $productsInfo [int:productId, arr:productDetails]
     * @param $shopId
     * @return array
     */
    public function getBoxesByProductsInfo(array $productsInfo, $shopId)
    {
        $ret = [];
        $pLayer = EkomApi::inst()->productLayer();
        foreach ($productsInfo as $productInfo) {
            list($id, $details) = $productInfo;
            $ret[] = $pLayer->getProductBoxModelByProductId($id, $shopId, null, $details);
        }
        return $ret;
    }
    //--------------------------------------------
    //
    //--------------------------------------------

}



