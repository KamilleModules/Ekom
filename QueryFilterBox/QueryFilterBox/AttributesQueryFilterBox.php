<?php


namespace Module\Ekom\QueryFilterBox\QueryFilterBox;


use Bat\UriTool;
use Module\Ekom\Api\EkomApi;
use QueryFilterBox\Query\Query;
use QueryFilterBox\QueryFilterBox\QueryFilterBox;

/**
 *
 * categoryId must be set.
 *
 *
 * Class AttributesQueryFilterBox
 * @package Module\Ekom\QueryFilterBox\QueryFilterBox
 */
class AttributesQueryFilterBox extends QueryFilterBox
{

    private $categoryId;

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public static function create()
    {
        return new static();
    }

    protected function doDecorateQuery(Query $query, array $pool, array &$usedPool)
    {
        $already = [];
        $model = [];
        $once = false;
        $attrApi = EkomApi::inst()->attributeLayer();
        $attributes = $attrApi->getAvailableAttributeByCategoryId($this->categoryId);
        $c = 0;
        foreach ($attributes as $attr) {
            $name = $attr['name'];
            if (!array_key_exists($name, $already)) {

                $already[$name] = null;


                //--------------------------------------------
                // PREPARING THE MODEL
                //--------------------------------------------
                /**
                 * The params are in the uri.
                 * Example:
                 * https://lee/category/tapis_de_marche?poids[0]=15_kg&poids[1]=4_kg
                 *
                 */
                $model[$name] = [
                    'title' => $attr['name_label'],
                    'type' => "items",
                    'items' => [],
                ];


                $get = $pool;
                $getArr = (array_key_exists($name, $pool)) ? $pool[$name] : [];

                $value = $attr['value'];
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
                    "uri" => $uri,
                    "selected" => $selected,
                ];


                //--------------------------------------------
                // TAKING CARE OF THE QUERY
                //--------------------------------------------
                if (array_key_exists($name, $pool)) {

                    $usedPool[] = $name;

                    $once = true;
                    $values = $pool[$name];
                    if (!is_array($values)) {
                        $values = [$values];
                    }
                    $safeIds = [];
                    foreach ($values as $v) {
                        if (false !== ($valueId = $attrApi->getAttrValueBySlug($v))) {
                            $safeIds[] = $valueId;
                        }
                    }


                    $tagName = "attrname" . $c;
                    $c++;

                    $query->addWhere("
a.name = :$tagName
and v.id in (" . implode(', ', $safeIds) . ")                          
                        ");
                    $query->addMarker($tagName, $name);


                }
            }
        }

        if (true === $once) {
            $query->addJoin("
inner join ek_product_has_product_attribute h on h.product_id=p.id
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id                            
");
        }


        $this->model = $model;

    }


}