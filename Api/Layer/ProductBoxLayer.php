<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\ProductBoxEntity;
use Module\Ekom\Api\Entity\ProductBoxEntityUtil;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\AttributeSelectorHelper;
use Module\Ekom\Utils\E;
use Module\EkomUserProductHistory\UserProductHistory\UserProductHistoryInterface;
use QuickPdo\QuickPdo;
use SqlQuery\SqlQueryInterface;


/**
 * What's the product box?
 * ===========================
 * When you navigate to the product page,
 * in most themes the product box is just this big block of product information at the top of the product page.
 *
 * This includes only the major information used by the widget of the same name.
 *
 * It doesn't include other information like the comments about a product, the features, the related items,
 * the bundled items,...
 *
 * The exhaustive properties of the box model are exposed here:
 * @see EkomModels::productBoxModel()
 *
 * So to recap, the product page is composed of many widgets, the product box being the most visible/important.
 * And the product box model is just the template model for displaying this product box widget.
 *
 *
 *
 */
class ProductBoxLayer
{


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param $productId
     *
     * @param array $selectedProductDetails , an array of name => value chosen by the user
     *
     * @param array|null $userContext
     */
    public static function getProductBoxByProductId(int $productId, array $selectedProductDetails = [], array $userContext = null)
    {

        $sqlQuery = ProductQueryBuilderUtil::getMaxiQuery($userContext);
        $sqlQuery->addWhere("and p.id=$productId");
        return self::getProductBoxBySqlQuery($sqlQuery, $selectedProductDetails, $userContext);

    }



    //--------------------------------------------
    // PRODUCT BOX
    //--------------------------------------------
    public static function getProductBoxByCardId($cardId, $productId = null, array $productDetailsArgs = [], array $gpc = null)
    {
        $e = ProductBoxEntity::create()
            ->setProductCardId($cardId)
            ->setProductId($productId)
            ->setProductDetails($productDetailsArgs);
        if (null !== $gpc) {
            $e->setGeneralContext($gpc);
        }

        return $e->getModel();
    }



    //--------------------------------------------
    // PRODUCT BOX LIST
    //--------------------------------------------
    public static function getProductBoxListByCardIds(array $cardIds, array $gpc = null)
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
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext($gpc);
        foreach ($cardIds as $cardId) {
            $boxes[] = ProductBoxEntity::create()
                ->setProductCardId($cardId)
                ->setGeneralContext($gpc)
                ->getModel();
        }
        return $boxes;
    }

    public static function getProductBoxListByProductIds(array $productIds, array $gpc = null)
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
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext($gpc);
        foreach ($id2cardIds as $productId => $cardId) {
            $boxes[] = ProductBoxEntity::create()
                ->setProductCardId($cardId)
                ->setProductId($productId)
                ->setGeneralContext($gpc)
                ->getModel();
        }
        return $boxes;
    }


    public static function getProductBoxListByCategoryName($categoryName, array $gpc = null)
    {
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext($gpc);
        $shopId = $gpc['shop_id'];
        $langId = $gpc['lang_id'];
        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getProductBoxListByCategoryName.$shopId.$langId.$categoryName");
        return A::cache()->get($hashString, function () use ($categoryName, $shopId, $gpc) {
            $ids = CategoryLayer::getCardIdsByCategoryName($categoryName, $shopId, true);
            return self::getProductBoxListByCardIds($ids, $gpc);
        });
    }

    /**
     * ek_product_group
     * @param $groupName
     * @return array
     */
    public static function getProductBoxListByGroupName($groupName, array $gpc = null)
    {
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext($gpc);
        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getProductBoxListByGroupName.$groupName");
        return A::cache()->get($hashString, function () use ($groupName, $gpc) {
            $ids = ProductGroupLayer::getProductIdsByGroup($groupName);
            return self::getProductBoxListByProductIds($ids, $gpc);
        });
    }



//    public static function getLastVisitedProductBoxList($userId, array $gpc = null)
//    {
//        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext($gpc);
//        $shopId = $gpc['shop_id'];
//        $langId = $gpc['lang_id'];
//        $hashString = ProductBoxEntityUtil::hashify("Ekom.ProductBoxLayer.getLastVisitedProductBoxListByCardId.$shopId.$langId.$userId");
//        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext();
//        return A::cache()->get($hashString, function () use ($userId, $shopId, $gpc) {
//            /**
//             * @var $history UserProductHistoryInterface
//             */
//            $boxes = [];
//            $history = X::get("EkomUserProductHistory_UserProductHistory");
//            $productsInfo = $history->getLastVisitedProducts($userId, 7);
//            $id2Details = [];
//            foreach ($productsInfo as $info) {
//                list($productId, $productDetails) = $info;
//
//                if ($productDetails) {
//                    $details = array_merge($productDetails['major'], $productDetails['minor']);
//                } else {
//                    $details = [];
//                }
//
//                $id2Details[$productId] = $details;
//            }
//            $id2cardIds = ProductCardLayer::getProductId2CardIdByProductIds(array_keys($id2Details));
//            foreach ($id2cardIds as $productId => $cardId) {
//                $details = $id2Details[$productId];
//                $boxes[] = ProductBoxEntity::create()
//                    ->setProductCardId($cardId)
//                    ->setProductId($productId)
//                    ->setProductDetails($details)
//                    ->setGeneralContext($gpc)
//                    ->getModel();
//            }
//            return $boxes;
//        });
//    }


    private static function getProductBoxBySqlQuery(SqlQueryInterface $sqlQuery, array $selectedProductDetails = [], array $userContext = null)
    {
        $q = $sqlQuery->getSqlQuery();
        $markers = $sqlQuery->getMarkers();
        $row = QuickPdo::fetch($q, $markers);

        MiniProductBoxLayer::sugarify($row);

        $row['selected_product_details'] = $selectedProductDetails;
        $productDetailsList = []; // todo: ask modules via hooks
        $row['product_details_list'] = $productDetailsList;


        $row['product_uri_with_details'] = $row['product_uri']; // if in doubt, recreate it from scratch
        if ($selectedProductDetails) {
            $row['product_uri_with_details'] .= "?" . http_build_query($selectedProductDetails);
        }


        //--------------------------------------------
        // IMAGES
        //--------------------------------------------
        $imagesInfo = ProductCardImageLayer::getProductBoxImagesInfoByProductCardId($row['product_card_id'], $row['product_id']);
        $row['images'] = $imagesInfo;


        //--------------------------------------------
        // COMMENTS
        //--------------------------------------------
        $ratingInfo = CommentLayer::getRatingInfo($row['product_card_id']);
        $row['rating_average'] = $ratingInfo['average'];
        $row['rating_nbVotes'] = $ratingInfo['count'];


        //--------------------------------------------
        // ATTRIBUTES
        //--------------------------------------------
        $productsInfo = ProductBoxEntityUtil::getProductCardProductsWithAttributes($row['product_card_id']);
        $attr = AttributeSelectorHelper::adaptProductWithAttributesToAttributesModel($productsInfo, $row['product_id']);
        $row['attributes_list'] = $attr;


        return $row;
    }


}