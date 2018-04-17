<?php


namespace Module\Ekom\Helper;


use Kamille\Services\XConfig;
use ListStaticParams\ListStaticParamsInterface;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\ListStaticParams\EkomListStaticParams;
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


    protected static function decorateSqlQueryWithListStaticParams(SqlQueryInterface $sqlQuery, ListStaticParamsInterface $listStaticParams)
    {
        $array = $listStaticParams->getArray();
        $pageKey = $array['pageKey'];
        $page = 1;
        if (array_key_exists($pageKey, $_GET)) {
            /**
             * Note that at this point we don't know the page boundaries
             * (they are computed only when the request is executed),
             * so we have to trust (the pagination plugin).
             */
            $page = (int)$_GET[$pageKey];
        }
        $nbItemsPerPage = $array['nbItemsPerPage'];
        $offset = ($page - 1) * $nbItemsPerPage;
        $sqlQuery->setLimit($offset, $nbItemsPerPage);

    }
}