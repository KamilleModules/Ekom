<?php


namespace Module\Ekom\Helper;


use Core\Services\Hooks;
use Module\Ekom\Api\Layer\CategoryCoreLayer;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use QuickPdo\QuickPdo;
use SqlQuery\SqlQueryInterface;

class SqlQueryHelper
{

    /**
     * @param string $search
     * @return SqlQueryInterface
     */
//    public static function getSqlQueryBySearchExpression(string $search)
//    {
//
//        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery([
//            "useAttributesString" => true, // this might change, maybe we don't need it...
//        ]);
//
//
//    }


    /**
     * @deprecated as we don't need new products to be defined automatically by the shop,
     * we rather promote a custom category.
     */
//    public static function getNewProductsSqlQuery()
//    {
//        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();
//        $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($categoryId);
//        if ($cardIds) {
//
//
//            /**
//             * New products are products added to the shop less than 60
//             */
//            $startDate = date("Y-m-d H:i:s");
//
//            $sCardIds = implode(', ', $cardIds);
//            $sqlQuery->addWhere("
//pr.date_added >= '$startDate'
//            ");
//            return $sqlQuery;
//        }
//        return false;
//    }


    /**
     * @param int $categoryId
     * @return SqlQueryInterface|false
     */
    public static function getCategoryWithFiltersSqlQueryByCategoryId(int $categoryId, array $options = [])
    {

        $queryOptions = $options['queryOptions'] ?? [
                "useAttributesString" => true,
            ];


        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery($queryOptions);
        $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($categoryId);
        if ($cardIds) {
            $sCardIds = implode(', ', $cardIds);
            $sqlQuery->addWhere(" and c.id in ($sCardIds)");
            return $sqlQuery;
        }
        return false;
    }


    public static function getBestSellersSqlQuery(array $options = [])
    {
        $queryOptions = $options['queryOptions'] ?? [];
        $limit = $options['limit'] ?? 50;
        $limit = (int)$limit;

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery($queryOptions);

        $sqlQuery->addField("pps.quantity as best_sell_quantity");


        /**
         * https://dev.mysql.com/doc/refman/8.0/en/example-maximum-column-group-row.html
         */
        $sqlQuery->addJoin("inner join 
(
	select 
	pps2.product_id,
	pps2.product_label,
	MAX(pps2.quantity) as quantity
	from ek_product_purchase_stat pps2
    group by product_id

) pps on pps.product_id=p.id  
        
        ");
        $sqlQuery->setLimit(0, $limit);
        return $sqlQuery;
    }


    public static function getCategorySqlQueryByCategoryName(string $categoryName, array $queryOptions = [])
    {

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery($queryOptions);
        $cardIds = CategoryLayer::getCardIdsByCategoryName($categoryName, true);

        if ($cardIds) {
            $sCardIds = implode(', ', $cardIds);
            $sqlQuery->addWhere(" and c.id in ($sCardIds)");
            return $sqlQuery;
        }
        return false;
    }


    public static function getLastVisitedSqlQuery(int $userId, int $limit = 10, array $excludedProductReferenceIds = [])
    {

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();


        $sqlQuery->addWhere("
and uvpr.user_id=$userId
        ");


        if ($excludedProductReferenceIds) {
            $sIds = implode(", ", array_map('intval', $excludedProductReferenceIds));
            $sqlQuery->addWhere("
and uvpr.product_reference_id not in($sIds)
        ");
        }


        $sqlQuery->addJoin("
inner join ek_user_visited_product_reference uvpr on uvpr.product_reference_id=pr.id
            ");


        $sqlQuery->setLimit(0, $limit);

        /**
         * Here, we believe showing the same product with all attributes variations
         * is not really interesting, we prefer to show only different cards.
         *
         */
        $sqlQuery->setGroupBy([
            "c.id",
        ]);


        return $sqlQuery;
    }


    public static function getUserWishListSqlQuery(int $userId, array $options = [])
    {

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();


        $sqlQuery->addWhere("
and uhpr.user_id=$userId
and uhpr.date_deleted is null
        ");


        $sqlQuery->addJoin("
inner join ek_user_has_product_reference uhpr on uhpr.product_reference_id=pr.id
            ");


        /**
         * Here, we believe showing the same product with all attributes variations
         * is not really interesting, we prefer to show only different cards.
         *
         */
        $sqlQuery->setGroupBy([
            "c.id",
        ]);


        return $sqlQuery;
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getFallbackCategoryWithFiltersSqlQueryByCategoryId(int $categoryId, array $options = [])
    {
        $parentCatIds = CategoryLayer::getParentCategoryIdsById($categoryId);
        while ($parentCatIds) {
            $catId = array_shift($parentCatIds);
            $queryOptions = $options['queryOptions'] ?? [
                    "useAttributesString" => true,
                ];


            $sqlQuery = ProductQueryBuilderUtil::getBaseQuery($queryOptions);
            $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($catId);
            if ($cardIds) {
                $sCardIds = implode(', ', $cardIds);
                $sqlQuery->addWhere(" and c.id in ($sCardIds)");

                $nbItems = (int)QuickPdo::fetch($sqlQuery->getCountSqlQuery(), $sqlQuery->getMarkers(), \PDO::FETCH_COLUMN);
                if (0 !== $nbItems) {
                    return $sqlQuery;
                }
            }
        }
        return false;
    }
}