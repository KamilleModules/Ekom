<?php


namespace Module\Ekom\Helper;


use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use SqlQuery\SqlQueryInterface;

class SqlQueryHelper
{

    /**
     * @param int $categoryId
     * @return SqlQueryInterface|false
     */
    public static function getCategoryWithFiltersSqlQueryByCategoryId(int $categoryId)
    {

        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();
        $cardIds = ProductCardLayer::getProductCardIdsByCategoryId($categoryId);
        if ($cardIds) {


            $sCardIds = implode(', ', $cardIds);
            $sqlQuery->addWhere(" and c.id in ($sCardIds)");


//        self::decorateSqlQueryWithListStaticParams($sqlQuery, $listParams);
            return $sqlQuery;
        }
        return false;
    }
}