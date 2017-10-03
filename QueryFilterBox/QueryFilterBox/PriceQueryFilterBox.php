<?php


namespace Module\Ekom\QueryFilterBox\QueryFilterBox;


use ListModifier\Util\ListModifierUtil;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
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
class PriceQueryFilterBox extends QueryFilterBox
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

                $this->model = [
                    "formTrail" => $formTrail,
                    "title" => "Price",
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