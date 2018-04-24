<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\Hooks;
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
     * This should be called only from an ajax service.
     * Normally, you would have the product_reference_id available and so you would use the
     * product reference id (getProductBoxByReferenceId method below).
     * However on the product page, it's just easier to pass the product id plus the product details,
     * let the modules resolve those to a product reference id, and then use the getProductBoxByReferenceId method.
     *
     * Note that the product box layer is the only case I know of which use this technique,
     * all other use cases make use of the product reference id (the cart, links in general)
     *
     *
     *
     * @param $productId
     * @param array $selectedProductDetails , an array of name => value chosen by the user
     */
    public static function getProductBoxByProductId(int $productId, array $selectedProductDetails = [])
    {
        $referenceId = null;
        Hooks::call("Ekom_ProductBox_getReferenceIdByProductId", $referenceId, $productId, $selectedProductDetails);
        if (null === $referenceId) {
            /**
             * If null, we assume that you don't use product details, and so we assume that your product
             * only relies on product attribute (recommended).
             */
            $referenceId = ProductReferenceLayer::getFirstProductReferenceIdByProductId($productId);
        }
        return self::getProductBoxByProductReferenceId($referenceId);

    }

    public static function getProductBoxByProductReferenceId(int $productReferenceId)
    {
        $sqlQuery = ProductQueryBuilderUtil::getMaxiQuery();
        $sqlQuery->addWhere("and pr.id=$productReferenceId");
        return self::getProductBoxBySqlQuery($sqlQuery);

    }


    //--------------------------------------------
    // PRODUCT BOX LIST
    //--------------------------------------------

    private static function getProductBoxBySqlQuery(SqlQueryInterface $sqlQuery)
    {
        $q = $sqlQuery->getSqlQuery();
        $markers = $sqlQuery->getMarkers();
        $row = QuickPdo::fetch($q, $markers);

        MiniProductBoxLayer::sugarify($row);


        $selectedProductDetails = []; // todo: ask modules via hooks
        $productDetailsList = []; // todo: ask modules via hooks, use same structure as attribute list...
        $row['selected_product_details'] = $selectedProductDetails;
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