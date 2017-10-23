<?php


namespace Module\Ekom\QueryFilterBox\QueryFilterBox;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QueryFilterBox\Collectable\CollectableInterface;
use QueryFilterBox\Query\Query;
use QueryFilterBox\QueryFilterBox\QueryFilterBox;
use QueryFilterBox\Util\Helper\QueryFilterBoxHelper;

/**
 *
 * The following variables should be set:
 * - categoryId
 *
 *
 * Class PriceQueryFilterBox
 * @package Module\Ekom\QueryFilterBox\QueryFilterBox
 */
class PriceQueryFilterBox extends QueryFilterBox implements CollectableInterface
{

    private $categoryId;
    private $_min;
    private $_max;


    public function __construct()
    {
        $this->categoryId = null;
        $this->_min = null;
        $this->_max = null;
    }

    public static function create()
    {
        return new static();
    }


    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }


    public function prepare()
    {
        if (null !== $this->categoryId) {

            $minMax = EkomApi::inst()->productLayer()->getMinMaxSalePrice($this->categoryId);
            $isB2b = E::isB2b();


            if (true === $isB2b) {
                $min = (float)$minMax['minSalePriceWithoutTax'];
                $max = (float)$minMax['maxSalePriceWithoutTax'];
            } else {
                $min = (float)$minMax['minSalePriceWithTax'];
                $max = (float)$minMax['maxSalePriceWithTax'];
            }



            if ($min !== $max) {

                $currentMin = $min;
                $currentMax = $max;

                $formTrail = QueryFilterBoxHelper::toFormFields($this->usedPool, ['price']);

                if(null !== $this->_min){
                    $currentMin = $this->_min;
                }
                if(null !== $this->_max){
                    $currentMax = $this->_max;
                }


                $this->model = [
                    "formTrail" => $formTrail,
                    "title" => "Prix",
                    "type" => "minMax",
                    "minValue" => $this->formatPrice($min),
                    "maxValue" => $this->formatPrice($max),
                    "currentMin" => $this->formatPrice($currentMin),
                    "currentMax" => $this->formatPrice($currentMax),
                    "moneyFormatArgs" => E::conf("moneyFormatArgs"),
                ];
            }
        }
    }


    //--------------------------------------------
    // COLLECTABLE
    //--------------------------------------------
    public function collect($param, $value)
    {
        if ('price' === $param) {
            if (null !== $this->_min) {
                return [
                    'keyLabel' => 'Prix',
                    'valueLabel' => E::price($this->_min) . " - " . E::price($this->_max),
                ];
            }
        }
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    protected function doDecorateQuery(Query $query, array $pool, array &$usedPool)
    {
        $this->_min = null;
        $this->_max = null;

        if (array_key_exists("price", $pool)) {
            $usedPool[] = 'price';

            $p = explode('-', $pool['price'], 2);

            if (2 === count($p)) {
                $min = (float)$p[0];
                $max = (float)$p[1];


                $this->_min = $min;
                $this->_max = $max;

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