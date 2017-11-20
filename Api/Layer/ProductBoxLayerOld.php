<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\ProductBoxEntity;
use Module\Ekom\Api\Entity\ProductBoxEntityUtil;
use Module\Ekom\Utils\E;
use Module\EkomUserProductHistory\UserProductHistory\UserProductHistoryInterface;


/**
 * Implementation note:
 * ------------------------
 * In various locations in this class, the shop_id and lang_id arguments are internalized,
 * that's because we want to avoid potential inconsistencies:
 * the hashString used for caching uses the ekom product box context which contains the shop_id and lang_id.
 *
 * Note: the E::getShopId is equivalent to do ProductBoxEntityUtil::getProductBoxGeneralContext()["shop_id"].
 * Note2: this also means that if you want to change the shop_id, you can use ApplicationRegistry::set("shop_id", 6),
 * if the pbc was not cached already, or update the pbc directly, which is also stored in the ApplicationRegistry
 * with the ekom.gpc key (see ProductBoxEntityUtil for more info).
 *
 *
 * Implementation note2:
 * -----------------------
 * to create a list of boxes, use the getProductBoxListByGroupName example,
 * which takes into account how the product box context should be handled.
 *
 *
 *
 *
 */
class ProductBoxLayerOld
{


    //--------------------------------------------
    // PRODUCT BOX
    //--------------------------------------------
    public static function getProductBoxByCardId($cardId, $productId = null, array $productDetailsArgs = [])
    {
        return ProductBoxEntity::create()
            ->setProductCardId($cardId)
            ->setProductId($productId)
            ->setProductDetails($productDetailsArgs)
            ->getModel();
    }

    public static function getProductBoxByProductId($productId, array $productDetailsArgs = [])
    {
        if (false !== ($cardId = ProductCardLayer::getIdByProductId($productId))) {
            return ProductBoxEntity::create()
                ->setProductCardId($cardId)
                ->setProductId($productId)
                ->setProductDetails($productDetailsArgs)
                ->getModel();
        }

        return [
            'errorCode' => "productIdNotFound",
            'errorTitle' => "Product id not found",
            'errorMessage' => "Product id $productId was not found in this database",
        ];
    }


    //--------------------------------------------
    // PRODUCT BOX LIST
    //--------------------------------------------
    public static function getProductBoxListByCardIds(array $cardIds, array $generalProductContext = null)
    {
        /**
         * This is a low level method and is not cached.
         *
         * This method is not cached for a few reasons:
         *
         * - cart uses them
         * - developer could use them as tool for their own development, and we don't want the
         *      cache to interfere with that
         *
         * You should use a wrapper if speed is an issue.
         */
        $boxes = [];
        if (null === $generalProductContext) {
            $generalProductContext = ProductBoxEntityUtil::getProductBoxGeneralContext();
        }
        foreach ($cardIds as $cardId) {
            $boxes[] = ProductBoxEntity::create()
                ->setProductCardId($cardId)
                ->setGeneralContext($generalProductContext)
                ->getModel();
        }
        return $boxes;
    }

    public static function getProductBoxListByProductIds(array $productIds)
    {
        /**
         * This is a low level method and is not cached.
         *
         * This method is not cached for a few reasons:
         *
         * - cart uses them
         * - developer could use them as tool for their own development, and we don't want the
         *      cache to interfere with that
         *
         * You should use a wrapper if speed is an issue.
         */
        $id2cardIds = ProductCardLayer::getProductId2CardIdByProductIds($productIds);
        $boxes = [];
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext();
        foreach ($id2cardIds as $productId => $cardId) {
            $boxes[] = ProductBoxEntity::create()
                ->setProductCardId($cardId)
                ->setProductId($productId)
                ->setGeneralContext($gpc)
                ->getModel();
        }
        return $boxes;
    }


    public static function getProductBoxListByCategoryName($categoryName, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getProductBoxListByCategoryName.$categoryName.$shopId");
        return A::cache()->get($hashString, function () use ($categoryName, $shopId) {
            $ids = CategoryLayer::getCardIdsByCategoryName($categoryName, $shopId, true);
            return self::getProductBoxListByCardIds($ids);
        });
    }

    /**
     * ek_product_group
     * @param $groupName
     * @return array
     */
    public static function getProductBoxListByGroupName($groupName, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getProductBoxListByGroupName.$groupName.$shopId");
        return A::cache()->get($hashString, function () use ($groupName, $shopId) {
            $ids = ProductGroupLayer::getProductIdsByGroup($groupName, $shopId);
            return self::getProductBoxListByProductIds($ids);
        });
    }

    /**
     * ek_product_group
     * @param $cardId , int
     * @return array
     */
    public static function getRelatedProductBoxListByCardId($cardId, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getRelatedProductBoxListByCardId.$cardId.$shopId");
        return A::cache()->get($hashString, function () use ($cardId, $shopId) {
            $ids = RelatedProductLayer::getRelatedProductIds($cardId, $shopId);
            return self::getProductBoxListByProductIds($ids);
        });
    }


    public static function getLastVisitedProductBoxList($userId, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getLastVisitedProductBoxListByCardId.$shopId.$userId");
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext();
        return A::cache()->get($hashString, function () use ($userId, $shopId, $gpc) {
            /**
             * @var $history UserProductHistoryInterface
             */
            $boxes = [];
            $history = X::get("EkomUserProductHistory_UserProductHistory");
            $productsInfo = $history->getLastVisitedProducts($userId, 7);
            $id2Details = [];
            foreach ($productsInfo as $info) {
                list($productId, $productDetails) = $info;

                if ($productDetails) {
                    $details = array_merge($productDetails['major'], $productDetails['minor']);
                } else {
                    $details = [];
                }

                $id2Details[$productId] = $details;
            }
            $id2cardIds = ProductCardLayer::getProductId2CardIdByProductIds(array_keys($id2Details));
            foreach ($id2cardIds as $productId => $cardId) {
                $details = $id2Details[$productId];
                $boxes[] = ProductBoxEntity::create()
                    ->setProductCardId($cardId)
                    ->setProductId($productId)
                    ->setProductDetails($details)
                    ->setGeneralContext($gpc)
                    ->getModel();
            }
            return $boxes;
        });
    }


}