<?php


namespace Module\Ekom\SqlQueryWrapper\Plugins;


use Bat\UriTool;
use QuickPdo\QuickPdo;
use SqlQuery\SqlQueryInterface;
use SqlQueryWrapper\Plugins\SqlQueryWrapperBasePlugin;

class EkomSqlQueryWrapperAttributeFilterPlugin extends SqlQueryWrapperBasePlugin implements EkomSummaryFilterHelperInterface
{

    protected $categoryId;
    protected $attributes;


    public function __construct()
    {
        parent::__construct();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getSummaryItemLabel(string $param, $value)
    {
        foreach ($this->attributes as $info) {
            if ($param === $info['attribute_name'] && $value === $info['value_name']) {
                return $info["attribute_label"] . ": " . $info['value_label'];
            }
        }
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    public function onQueryReady(SqlQueryInterface $sqlQuery)
    {
        $sqlQueryString = $sqlQuery->getSqlQuery();
        $markers = $sqlQuery->getMarkers();

        $q = "
select
a.id as attribute_id,
a.name as attribute_name,
a.label as attribute_label,
v.id as value_id,
v.value as value_name,
v.label as value_label,
count(v.id) as count

from ek_product_has_product_attribute h 
inner join ek_product_attribute a on a.id=h.product_attribute_id 
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id
where h.product_id in (select product_id from ($sqlQueryString) as zz )

group by v.value
 
                
                ";
        $this->attributes = QuickPdo::fetchAll($q, $markers);
    }


    public function prepareQuery(SqlQueryInterface $sqlQuery)
    {
        /**
         * Again, following the "if it ain't broken don't fix it" methodology.
         * (but having some reserve on the written code)`
         *
         * comes from there btw:
         * class-modules/Ekom/HybridList/HybridListControl/Filter/AttributesFilterHybridListControl.php
         */
        $this->prepareQueryOld($sqlQuery);
    }

    public function prepareModel(int $nbItems, array $rows)
    {
        /**
         * If it works, don't fix it they say...
         * If I had to redo it though, I would certainly put more intelligence on the
         * view side, as to delegate some of the work done here...
         */
        $this->model = $this->getOldModel();
    }


    public function setCategoryId(int $categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getOldModel()
    {

        $pool = $_GET;
        $model = [];
        $attributes = $this->attributes;

        foreach ($attributes as $attr) {

            $name = $attr['attribute_name'];

            if (!array_key_exists($name, $model)) {
                $model[$name] = [
                    'title' => $attr['attribute_label'],
                    'type' => "items",
                    'items' => [],
                ];
            }


            //--------------------------------------------
            // PREPARING THE MODEL
            //--------------------------------------------
            /**
             * The params are in the uri.
             * Example:
             * https://lee/category/tapis_de_marche?poids[0]=15_kg&poids[1]=4_kg
             *
             */
            $get = $pool;
            $getArr = (array_key_exists($name, $pool)) ? $pool[$name] : [];

            $value = $attr['value_name'];
            if (
                (is_array($getArr) && in_array($value, $getArr, true)) ||
                (!is_array($getArr) && $value === $getArr)
            ) {
                /**
                 * If the attribute already exist in the uri, we remove it from the uri
                 * params (toggle like behaviour)
                 */
                $selected = true;
                if (is_array($getArr)) {
                    if (false !== ($index = array_search($value, $getArr))) {
                        unset($get[$name][$index]);
                        if (0 === count($get[$name])) {
                            unset($get[$name]);
                        }
                    }
                } else {
                    unset($get[$name]);
                }
            } else {
                $selected = false;
                if (true === is_array($getArr)) {
                    if (false === array_search($value, $getArr)) {
                        $get[$name][] = $value;
                    }
                } else {
                    $get[$name] = $value;
                }
            }

            $uri = UriTool::uri(null, $get);
            $model[$name]['items'][] = [
                "label" => $attr['value_label'],
                "value" => $attr['value_name'],
                "count" => $attr['count'],
                "uri" => $uri,
                "selected" => $selected,
            ];

        }

        return $model;
    }


    private function prepareQueryOld(SqlQueryInterface $sqlQuery)
    {
        $attributes = $this->attributes;
        $pool = $_GET;
        $whereBits = [];

        //--------------------------------------------
        // filtering only attributes that we actually use
        //--------------------------------------------
        $attrValue2Id = [];
        foreach ($attributes as $k => $attr) {
            if (!array_key_exists($attr['attribute_name'], $pool)) {
                unset($attributes[$k]);
            } else {
                $attrValue2Id[$attr['value_name']] = $attr['value_id'];
            }
        }

        $already = [];
        $atLeastOneAttribute = false;

        $c = 0;
        foreach ($attributes as $attr) {
            $name = $attr['attribute_name'];


            //--------------------------------------------
            // TAKING CARE OF THE QUERY
            //--------------------------------------------
            if (!array_key_exists($name, $already)) {
                $already[$name] = null;

                if (array_key_exists($name, $pool)) {

                    $usedPool[] = $name;


                    $values = $pool[$name];
                    if (!is_array($values)) {
                        $values = [$values];
                    }

                    $safeIds = []; // sometimes, the user might try non-existing values
                    foreach ($values as $attrValue) {
                        if (array_key_exists($attrValue, $attrValue2Id)) {
                            $safeIds[] = $attrValue2Id[$attrValue];
                        } else {
                            // this value is not found: the request should yield no results
                            // by the way, this is a hand made request (the website won't
                            // provide erroneous requests like that)
                            $safeIds[] = -1;
                        }
                    }


                    $tagName = "attrname" . $c++;

                    if ($safeIds) {

                        $atLeastOneAttribute = true;
//                        $whereBits[] = "
//(
//    a.name = :$tagName
//    and v.id in (" . implode(', ', $safeIds) . ")
//)
//
//                        ";
                        $whereBits[] = "
(
    attribute_name = :$tagName
    and attribute_value_id in (" . implode(', ', $safeIds) . ")
)
                          
                        ";

                        $sqlQuery->addMarker($tagName, $name);
                    }


                }
            }
        }


        if (true === $atLeastOneAttribute) { // note: maybe this could/should be unconditional?
            $sqlQuery->addField("
a.name as attribute_name,            
v.id as attribute_value_id            
            ");

            /**
             * Note: we use left join instead of inner joins, because
             * then we can combine the results with other filters like discount filter for instance.
             * (i.e. the user wants to select products having discount=10 OR attribute.weight=2kg,
             * but what if a product has discount 10 but doesn't have any attributes?)
             */
            $sqlQuery->addJoin("
left join ek_product_has_product_attribute phpa on phpa.product_id=p.id
left join ek_product_attribute a on a.id=phpa.product_attribute_id
left join ek_product_attribute_value v on v.id=phpa.product_attribute_value_id                            
");
//            $where = "and (" . implode(" or ", $whereBits) . ")";
            $where = "(" . implode(" or ", $whereBits) . ")";
//            $sqlQuery->addWhere($where);
            $sqlQuery->addHaving($where, "group1");
        }
    }
}

