<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\ProductBoxEntity;
use Module\Ekom\Api\Entity\ProductBoxEntityUtil;
use Module\Ekom\Utils\E;

class ProductBoxLayer
{


    //--------------------------------------------
    // PRODUCT BOX
    //--------------------------------------------
    public static function getProductBoxByCardId($cardId, $productId = null, array $productDetails = [], $shopId = null, $langId = null)
    {
        $boxEntity = ProductBoxEntity::create()
            ->setProductCardId($cardId)
            ->setProductId($productId)
            ->setProductDetails($productDetails)
            ->setShopId($shopId)
            ->setLangId($langId);
        return $boxEntity->getModel();
    }

    public static function getProductBoxByProductId($productId, array $productDetails = [], $shopId = null, $langId = null)
    {
        if (false !== ($cardId = ProductCardLayer::getIdByProductId($productId))) {
            $boxEntity = ProductBoxEntity::create()
                ->setProductCardId($cardId)
                ->setProductId($productId)
                ->setProductDetails($productDetails)
                ->setShopId($shopId)
                ->setLangId($langId);
            return $boxEntity->getModel();
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
    public static function getProductBoxListByCardIds(array $cardIds, $shopId = null, $langId = null, array $generalProductContext = null)
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
                ->setShopId($shopId)
                ->setLangId($langId)
                ->setGeneralContext($generalProductContext)
                ->getModel();
        }
        return $boxes;
    }

    public static function getProductBoxListByProductIds(array $productIds, $shopId = null, $langId = null)
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
        a("ali");
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext();
        a("ali", $gpc);
        foreach ($id2cardIds as $productId => $cardId) {
            $boxes[] = ProductBoxEntity::create()
                ->setProductCardId($cardId)
                ->setProductId($productId)
                ->setShopId($shopId)
                ->setLangId($langId)
                ->setGeneralContext($gpc)
                ->getModel();
        }
        return $boxes;
    }


    /**
     * ek_product_group
     * @param $groupName
     * @return array
     */
    public static function getProductBoxListByGroupName($groupName, $shopId = null)
    {
        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getProductBoxListByGroupName.$shopId.$groupName");
        return A::cache()->get($hashString, function () use ($groupName, $shopId) {
            $ids = ProductGroupLayer::getProductIdsByGroup($groupName, $shopId);
            return self::getProductBoxListByProductIds($ids, $shopId);
        }, self::getCacheIdentifiers([
            'ek_product_group_has_product',
            'ek_product_group',
        ]));
    }



    //--------------------------------------------
    //
    //--------------------------------------------

    private static function getCacheIdentifiers(array $cacheIdentifiers)
    {
        $boxIdentifiers = ProductBoxEntity::getCacheDeleteIdentifiers();
        return array_unique(array_merge($boxIdentifiers, $cacheIdentifiers));
    }

}