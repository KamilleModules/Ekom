<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\EkomUserProductHistory\UserProductHistory\UserProductHistoryInterface;

class ProductSelectionLayer
{


    public function getProductBoxModelsByGroup($productGroupName, $shopId = null)
    {
        if (null === $shopId) {
            $shopId = E::getShopId();
        }
        $shopId = (int)$shopId;

        return A::cache()->get("Ekom.ProductSelectionLayer.getProductBoxModelsByGroup.$shopId.$productGroupName", function () use ($productGroupName, $shopId) {
            $ids = EkomApi::inst()->productGroupLayer()->getProductIdsByGroup($productGroupName, $shopId);
            return $this->getBoxesByIds($ids, $shopId);
        }, [
            // ProductGroupLayer.getProductIdsByGroup
            'ek_product_group_has_product',
            'ek_product_group',
        ]);
    }


    public function getProductBoxModelsByRelatedId($cardId, $shopId = null)
    {
        if (null === $shopId) {
            $shopId = E::getShopId();
        }
        $shopId = (int)$shopId;

        return A::cache()->get("ProductSelectionLayer.getProductBoxModelsByRelatedId.$shopId.$cardId", function () use ($cardId, $shopId) {
            $ids = EkomApi::inst()->relatedProductLayer()->getRelatedProductIds($cardId, $shopId);
            return $this->getBoxesByIds($ids, $shopId);

        }, [
            // RelatedProductLayer.getRelatedProductIds
            'ek_product_group_has_product',
            'ek_product_group',
        ]);
    }


    public function getProductBoxModelsByLastVisited($userId, $shopId = null)
    {
        return A::cache()->get("ProductSelectionLayer.getProductBoxModelsByLastVisited.$shopId.$userId", function () use ($userId, $shopId) {

            /**
             * @var $history UserProductHistoryInterface
             */
            $history = X::get("EkomUserProductHistory_UserProductHistory");
            $ids = $history->getLastVisitedProductIds($userId, 7);
            return $this->getBoxesByIds($ids, $shopId);

        }, [
            "ekom_user_visited_product_history.$userId", // see FileSystemUserProductHistory
        ]);
    }


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

    public function getBoxesByIds(array $ids, $shopId)
    {
        $ret = [];
        $pLayer = EkomApi::inst()->productLayer();
        foreach ($ids as $id) {
            $ret[] = $pLayer->getProductBoxModelByProductId($id, $shopId);
        }
        return $ret;
    }
    //--------------------------------------------
    //
    //--------------------------------------------

}



