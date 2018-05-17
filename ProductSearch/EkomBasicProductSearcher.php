<?php


namespace Module\Ekom\ProductSearch;


use Bat\FileSystemTool;
use Bat\StringTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\MiniProductBoxLayer;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 * Another class again,
 * that's because the database has been reforged,
 * and now I bet we don't need the help of an extra "cache table" (as promoted by the EkomFastSearch module).
 * So, a simple search using the default tables here...
 *
 *
 * One drawback though, we loose the attr_string property.
 *
 * But in our company we don't use it for small items.
 * As said somewhere else, if we need attr_string, we can always create a dedicated cached table for that,
 * and still benefit the new ekom database model's all-products-in-one-query promise.
 * Actually even better, in the ek_product table, add an attribute_value_string
 * property which contains an array of attribute values' labels in csv form.
 * That could be a quick cache solution for that problem...
 *
 *
 * Also now we will return a miniBoxModel, which is becoming a standard model
 * for various usages of products in Ekom.
 * @see EkomModels::miniBoxModel()
 *
 *
 *
 *
 */
class EkomBasicProductSearcher extends AbstractProductSearch
{


    /**
     * @param $query
     * @return array, each entry being an entry with the following keys:
     *          - value: the label to display
     *          - data: the uri to the product or product card
     *
     */
    protected function doGetResults($query)
    {


        //--------------------------------------------
        // PRODUCTS
        //--------------------------------------------
        $products = MiniProductBoxLayer::getBoxesBySearchExpression($query);


        //--------------------------------------------
        // CATEGORIES
        //--------------------------------------------
        $categories = [];

        $rows = QuickPdo::fetchAll("

select label, `type`, slug 
from ek_category 
where 
label like :query

", [
            "query" => '%' . str_replace('%', '\%', $query) . '%',
        ]);


        $alreadyLabels = [];
        foreach ($rows as $row) {
            if (!in_array($row['label'], $alreadyLabels)) {
                $alreadyLabels[] = $row['label'];
                $row['uriCategory'] = E::link("Ekom_category", [
                    'slug' => $row['slug'],
                    'type' => $row['type'],
                ]);
                $categories[] = $row;
            }
        }


        $res = [
            'query' => $query,
            'products' => $products,
            'categories' => $categories,
        ];
        Hooks::call("Ekom_ProductSearch_onSearchQueryAfter", $res);
        return $res;

    }
}

