<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use ListParams\ListParams;
use ListParams\Model\QueryDecorator;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Helper\SqlQueryHelper;
use Module\Ekom\ListParams\ListBundleFactory\EkomListBundleFactoryHelper;
use Module\Ekom\SqlQueryWrapper\EkomProductListSqlQueryWrapper;
use Module\Ekom\SqlQueryWrapper\EkomSqlQueryWrapper;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class SearchResultsLayer
{

    /**
     * Here we do a search by card,
     * and we assume that the ek_shop_has_product_lang.label
     * are filled.
     *
     *
     * @return array
     */
    public function getModel()
    {
        $model = [];
        if (array_key_exists('search', $_GET)) {
            $search = $_GET['search'];
            ApplicationRegistry::set("ekom.breadcrumbs.label", "Votre résultat de recherche pour \"$search\"");

            $sqlQuery = MiniProductBoxLayer::getBoxesBySearchExpression($search, 2);
            $wrapper = EkomProductListSqlQueryWrapper::create()->setSqlQuery($sqlQuery);
            $wrapper->prepare();
            $nbItems = $wrapper->getNumberOfItems();


            $model = [
                "search" => $search,
                "nbProducts" => $nbItems,
                "listWrapper" => $wrapper,
            ];

        }
        return $model;


    }
}
