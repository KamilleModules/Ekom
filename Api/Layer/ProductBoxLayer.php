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


    public static function getRelatedProductBoxListByCardId(int $cardId, $type = null, array $options = [])
    {
        if (null !== $type) {
            $type = "-" . $type;
        }
        return self::getBoxesByProductGroupName(":related$type-$cardId", $options);
    }


    public static function getBoxesByProductGroupName(string $productGroupName, array $options = [])
    {

        $groupByProductId = $options['groupByProductId'] ?? false;

        $sqlQuery = ProductQueryBuilderUtil::getMaxiQuery();

        // specific to groups
        $sqlQuery->addWhere("
and g.name = :group_name        
        ");
        $sqlQuery->addJoin("
inner join ek_product_group_has_product phg on phg.product_id=p.id
inner join ek_product_group g on g.id=phg.product_group_id
            ");
        $sqlQuery->addMarker("group_name", $productGroupName);
        $sqlQuery->addOrderBy("phg.order", "asc");


        if (true === $groupByProductId) {
            $sqlQuery->setGroupBy([
                "p.id",
            ]);
        }


        $rows = QuickPdo::fetchAll((string)$sqlQuery, $sqlQuery->getMarkers());
        foreach ($rows as $k => $row) {
            self::sugarify($row);
            $rows[$k] = $row;
        }
        return $rows;
    }

    //--------------------------------------------
    // PRODUCT BOX LIST
    //--------------------------------------------
    private static function getProductBoxBySqlQuery(SqlQueryInterface $sqlQuery)
    {
        $q = $sqlQuery->getSqlQuery();
        $markers = $sqlQuery->getMarkers();


        $row = QuickPdo::fetch($q, $markers);
        self::sugarify($row);

        return $row;
    }


    private static function sugarify(array &$row)
    {

        MiniProductBoxLayer::sugarify($row);


        /**
         * @moduleDevelopers: if you are using product details,
         * then you should define at least the following properties:
         *
         * - selected_product_details: a map of user selected product details.
         * - product_details_list, the productModifiersListModel
         *
         * @see EkomModels::productModifiersListModel()
         */
        Hooks::call("Ekom_ProductBox_decorateBoxLayerModel", $row);
        $selectedProductDetails = $row['selected_product_details'] ?? [];
        $productDetailsList = $row['product_details_list'] ?? [];
        $attributes_list = $row['attributes_list'] ?? null;

        if (null === $attributes_list) {
            //--------------------------------------------
            // ATTRIBUTES
            //--------------------------------------------
            /**
             * Those are the default Ekom attributes.
             * Your module might want to override this default system IF
             * it usses both the attributes AND product details mechanism together
             */
            $productsInfo = ProductBoxEntityUtil::getProductCardProductsWithAttributes($row['product_card_id']);
            $attributes_list = AttributeSelectorHelper::adaptProductWithAttributesToAttributesModel($productsInfo, $row['product_id']);

        }


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
        $row['attributes_list'] = $attributes_list;


    }
}