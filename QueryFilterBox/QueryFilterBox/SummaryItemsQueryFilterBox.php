<?php


namespace Module\Ekom\QueryFilterBox\QueryFilterBox;


use ListModifier\Util\ListModifierUtil;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QueryFilterBox\Query\Query;
use QueryFilterBox\QueryFilterBox\QueryFilterBox;
use QueryFilterBox\Util\Helper\QueryFilterBoxHelper;

class SummaryItemsQueryFilterBox extends QueryFilterBox
{

    private $categoryId;


    public function __construct()
    {
        $this->categoryId = null;
    }

    public static function create()
    {
        return new static();
    }

    public function prepare()
    {
        $summaryItems = [];
        $attrApi = EkomApi::inst()->attributeLayer();
        $attributes = EkomApi::inst()->attributeLayer()->getAvailableAttributeByCategoryId($this->categoryId);

        a(__FILE__);
        az($attributes);



        foreach ($this->usedPool as $name) {

            if (array_key_exists($name, $this->pool)) {
                $value = $this->pool[$name];


                if (
                    (is_array($getArr) && in_array($value, $getArr, true)) ||
                    (!is_array($getArr) && $value === $getArr)
                ) {
                    /**
                     * If the attribute already exist in the uri,
                     * the value disappears from the list filter widget and goes to the summary
                     * widget (as a user current filter).
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
                    if (true === $isArray) {
                        if (false === array_search($value, $getArr)) {
                            $get[$name][] = $value;
                        }
                    } else {
                        $get[$name] = $value;
                    }
                }

                $uri = UriTool::uri(null, $get);
                if (true === $selected) {
                    $summaryItems[$name][] = [
                        'label' => $attr['value_label'],
                        'uri' => $uri,
                    ];
                }

            }
        }

        $this->model = $summaryItems;
    }


    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }






    //--------------------------------------------
    //
    //--------------------------------------------
    protected function doDecorateQuery(Query $query, array $pool, array &$usedPool)
    {
        if (array_key_exists("price", $pool)) {
            $usedPool[] = 'price';

            $p = explode('-', $pool['price'], 2);

            if (2 === count($p)) {
                $min = (float)$p[0];
                $max = (float)$p[1];

                $tagMin = "pricemin";
                $tagMax = "pricemax";

                $query->addWhere("
shp._sale_price_without_tax >= :$tagMin                
and shp._sale_price_without_tax <= :$tagMax                
                ");

                $query->addMarker($tagMin, $min);
                $query->addMarker($tagMax, $max);
            }
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function formatPrice($n)
    {
        return sprintf('%.2f', $n);
    }
}