<?php


namespace Module\Ekom\Helper;


use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use SqlQuery\SqlQueryInterface;

class SqlQueryHelper
{
    public static function getCategoryWithFiltersSqlQueryByCategoryId(int $categoryId): SqlQueryInterface
    {

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();
        $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($categoryId);
        $sCardIds = implode(', ', $cardIds);
        $sqlQuery->addWhere(" and c.id in ($sCardIds)");


//        self::decorateSqlQueryWithListStaticParams($sqlQuery, $listParams);
        return $sqlQuery;
    }
}