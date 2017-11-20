<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\Hooks;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;


/**
 * See more info in EkomDerbyCache.
 * Note: all caches should be executed in order for optimum results:
 * in particular it makes sense to cache the boxes BEFORE the box list, because if you do the contrary,
 * then you will delete (temporarily) the boxes used in the cached list, which is at least risky.
 *
 */
class CacheLayer
{

    //--------------------------------------------
    // ALL
    //--------------------------------------------
    public static function rebuildAll($categoryName, $shopId = null, $langId = null)
    {
        self::rebuildShop($shopId, $langId);
        self::rebuildProductGroups($shopId, $langId);
        self::rebuildBoxesByCategoryName($categoryName, $shopId, $langId);
        self::rebuildBoxLists($shopId, $langId);
        self::rebuildCategories($shopId, $langId);
        self::rebuildModules($shopId, $langId);
    }


    //--------------------------------------------
    // SHOP
    //--------------------------------------------
    public static function rebuildShop($shopId = null, $langId = null)
    {
        self::deleteShop($shopId, $langId);
        EkomApi::inst()->initWebContext($shopId);
        CarrierLayer::getCarriers($shopId);
        ShopLayer::getPhysicalAddresses(null, $shopId, $langId);
    }


    public static function deleteShop($shopId, $langId)
    {
        $info = ShopLayer::getShopItemById($shopId);
        $host = $info['host'];
        A::cache()->deleteByPrefix("Module.Ekom.Api.EkomApi.initWebContext.$host");
        A::cache()->deleteByPrefix("Module.Ekom.Api.EkomApi.initWebContext.quartet.$shopId-$langId");
        A::cache()->deleteByPrefix("Ekom.CarrierLayer.getCarriers.$shopId");
        A::cache()->deleteByPrefix("Ekom.ShopLayer.getPhysicalAddresses.$shopId.$langId.");
    }


    //--------------------------------------------
    // PRODUCT GROUPS
    //--------------------------------------------
    public static function rebuildProductGroups($shopId = null, $langId = null)
    {
        self::deleteProductGroups($shopId);
        $groupNames = ProductGroupLayer::getAllGroupNames($shopId);
        foreach ($groupNames as $groupName) {
            ProductGroupLayer::getProductIdsByGroup($groupName, $shopId);
        }
    }


    public static function deleteProductGroups($shopId)
    {
        A::cache()->deleteByPrefix("Ekom.ProductGroupLayer.getProductIdsByGroup.$shopId.");
    }


    //--------------------------------------------
    // BOXES
    //--------------------------------------------
    public static function deleteBoxesByCategoryName($categoryName, $shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getShopId($langId);
        $cardIds = CategoryLayer::getCardIdsByCategoryName($categoryName, $shopId, true);
        foreach ($cardIds as $cardId) {
            A::cache()->deleteByPrefix("Ekom/figure/productBox-$shopId-$langId-$cardId-");
//            A::cache()->deleteByPrefix("Ekom.ProductBoxLayer.getProductBoxListByCategoryName.$categoryName");
        }
    }

    public static function rebuildBoxesByCategoryName($categoryName, $shopId = null, $langId = null)
    {
        self::deleteBoxesByCategoryName($categoryName, $shopId, $langId);
        $cardIds = CategoryLayer::getCardIdsByCategoryName($categoryName, $shopId, true);
        foreach ($cardIds as $cardId) {
            ProductBoxLayer::getProductBoxByCardId($cardId);
            $productIds = ProductCardLayer::getProductIds($cardId);
            foreach ($productIds as $productId) {
                ProductBoxLayer::getProductBoxByProductId($productId);
            }

        }
    }

    //--------------------------------------------
    // BOX LISTS
    //--------------------------------------------
    public static function rebuildBoxLists($shopId = null, $langId = null)
    {
        self::deleteBoxLists($shopId, $langId);
        $groupNames = ProductGroupLayer::getAllGroupNames($shopId);
        foreach ($groupNames as $groupName) {
            ProductBoxLayer::getProductBoxListByGroupName($groupName, [
                'shop_id' => $shopId,
                'lang_id' => $langId,
            ]);
        }
    }

    public static function deleteBoxLists($shopId = null, $langId = null)
    {
        A::cache()->deleteByPrefix("Ekom.ProductBoxLayer.getProductBoxListByGroupName.$shopId.$langId.");
    }


    //--------------------------------------------
    // CATEGORIES
    //--------------------------------------------
    public static function rebuildCategories($shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        self::deleteCategories($shopId, $langId);

        // now prepare category figures (actually, we do separate items for now..., implementation detail)
        $infos = CategoryCoreLayer::create()->getSelfAndChildren("home", -1, $shopId);
        $catLayer = EkomApi::inst()->categoryLayer();
        foreach ($infos as $info) {
            $categoryId = $info['id'];
            $categoryName = $info['name'];
            CategoryCoreLayer::create()->getSelfAndChildren($categoryName, 1, $shopId, $langId);


            $catLayer->getSubCategoriesByName($categoryName, 0, "", $shopId, $langId);
            $catLayer->getSubCategoriesById($categoryId, 0, $shopId, $langId);
            CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($categoryId, -1, $shopId, $langId);
            CategoryCoreLayer::create()->getSelfAndParentsByCategoryId($categoryId, -1, $shopId, $langId);


            if (1 === $info['depth']) {
                $catLayer->getSubCategoriesByName($categoryName, 1, "", $shopId, $langId);
            }


            ProductCardLayer::getProductCardIdsByCategoryId($categoryId, $shopId);
            AttributeLayer::getAvailableAttributeByCategoryId($categoryId, $shopId, $langId);
        }



        $productIds = ProductLayer::getIds($shopId);
        foreach ($productIds as $productId) {
            $catLayer->getCategoryIdTreeByProductId($productId, $shopId, $langId);
        }

    }

    public static function deleteCategories($shopId, $langId)
    {
        $cache = A::cache();
        $cache->deleteByPrefix("Ekom.CategoryLayer.getSubCategoriesByName.$shopId.$langId.");
        $cache->deleteByPrefix("Ekom.CategoryLayer.getSubCategoriesById.$shopId.$langId.");
        $cache->deleteByPrefix("Ekom.CategoryLayer.getCategoryIdTreeByProductId.$shopId.$langId.");
        $cache->deleteByPrefix("Ekom.ProductCardLayer.getProductCardIdsByCategoryId.$shopId.");
        $cache->deleteByPrefix("Ekom.AttributeLayer.getAvailableAttributeByCategoryId.$shopId.$langId.");
        $cache->deleteByPrefix("Ekom/CategoryCoreLayer/getSelfAndParentsByCategoryId-$shopId.$langId.");
        $cache->deleteByPrefix("Ekom/CategoryCoreLayer/getSelfAndChildren-$shopId.$langId.");
    }



    //--------------------------------------------
    // MODULES
    //--------------------------------------------
    public static function rebuildModules($shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        self::deleteModules($shopId, $langId);


        Hooks::call("Ekom_CacheLayer_rebuildModules", $shopId, $langId);
    }

    public static function deleteModules($shopId, $langId)
    {
        Hooks::call("Ekom_CacheLayer_deleteModules", $shopId, $langId);
    }


}
