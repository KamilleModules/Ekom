<?php


namespace Module\Ekom\SqlQueryWrapper\Plugins;


use Kamille\Services\XConfig;
use Module\Ekom\Utils\E;
use SqlQuery\SqlQueryInterface;
use SqlQueryWrapper\Plugins\SqlQueryWrapperBasePlugin;

class EkomSqlQueryWrapperPriceFilterPlugin extends SqlQueryWrapperBasePlugin implements EkomSummaryFilterHelperInterface
{

    protected $range;
    protected $priceKey;
    protected $pageKey;


    public function __construct()
    {
        parent::__construct();
        $this->range = [0, 10000];
        $this->priceKey = "price";
        $this->pageKey = "page";
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function getSummaryItemLabel(string $param, $value)
    {
        if ($this->priceKey === $param) {
            $p = explode('-', $value);
            return 'Prix: ' . E::price($p[0]) . " - " . E::price($p[1]);
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function prepareQuery(SqlQueryInterface $sqlQuery)
    {
        if (array_key_exists($this->priceKey, $_GET)) {

            list($min, $max) = explode('-', $_GET[$this->priceKey]);
            $min = (int)$min;
            $max = (int)$max;
            $sqlQuery->addHaving("sale_price between $min and $max");
        }
    }

    public function prepareModel(int $nbItems, array $rows)
    {

        list($minValue, $maxValue) = $this->range;
        $min = $minValue;
        $max = $maxValue;

        if (array_key_exists($this->priceKey, $_GET)) {
            list($min, $max) = explode('-', $_GET[$this->priceKey]);
            $min = (int)$min;
            $max = (int)$max;
        }

        $this->model = [
            "minValue" => $minValue,
            "maxValue" => $maxValue,
            "currentMin" => $min,
            "currentMax" => $max,
            "pageKey" => $this->pageKey,
            "priceKey" => $this->priceKey,
            "moneyFormatArgs" => XConfig::get("Ekom.moneyFormatArgs"),
        ];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function setRange($minValue, $maxValue)
    {
        $this->range = [$minValue, $maxValue];
        return $this;
    }

    public function setPriceKey(string $priceKey)
    {
        $this->priceKey = $priceKey;
        return $this;
    }

    public function setPageKey(string $pageKey)
    {
        $this->pageKey = $pageKey;
        return $this;
    }


}

