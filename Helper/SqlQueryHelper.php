<?php


namespace Module\Ekom\Helper;


use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use SqlQuery\SqlQueryInterface;

class SqlQueryHelper
{

    /**
     * @param string $search
     * @return SqlQueryInterface
     */
    public static function getSqlQueryBySearchExpression(string $search)
    {

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery([
            "useAttributesString" => true, // this might change, maybe we don't need it...
        ]);


    }

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


    public static function getCategorySqlQueryByCategoryName(string $categoryName)
    {

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();
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


}