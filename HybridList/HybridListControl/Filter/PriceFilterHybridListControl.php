<?php


namespace Module\Ekom\HybridList\HybridListControl\Filter;


use Bat\UriTool;
use HybridList\HybridListControl\HybridListControl;
use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;
use Module\Ekom\Utils\E;

class PriceFilterHybridListControl extends HybridListControl implements SummaryFilterAwareInterface
{

    private $_alreadyReacted;
    private $context;
    private $userMin;
    private $userMax;

    public function __construct()
    {
        parent::__construct();
        $this->_alreadyReacted = false;
        $this->userMin = null;
        $this->userMax = null;
        $this->context = [];
    }

    public function prepareHybridList(HybridListInterface $list, array $context)
    {
        $this->_alreadyReacted = false;
        $this->context = $context;

        //--------------------------------------------
        // shape request
        //--------------------------------------------
        $list
            ->addListShaper(ListShaper::create()
                ->setPriority(100)
                ->reactsTo("price")
                ->setExecuteCallback(function ($input, array &$boxes, array &$info = [], $originalBoxes) use ($context) {
                    $p = explode('-', $input, 2);
                    $min = null;
                    $max = null;

                    if (2 === count($p)) {
                        // we don't use pennies
                        $min = (int)min($p);
                        $max = (int)max($p);
                        foreach ($boxes as $k => $box) {
                            $price = $box['priceSaleRaw'];
                            if ($price < $min || $price > $max) {
                                unset($boxes[$k]);
                                $info['totalNumberOfItems'] = $info['totalNumberOfItems'] - 1;
                            }
                        }
                    }

                    $this->userMin = $min;
                    $this->userMax = $max;
                })
            );


        return $this;

    }


    public function getSummaryFilterItem($param, $value)
    {
        if ('price' === $param) {
            $p = explode('-', $value);
            return 'Prix: ' . E::price($p[0]) . " - " . E::price($p[1]);
        }
    }


    public function getModel()
    {
        if (empty($this->model)) {

            //--------------------------------------------
            // prepare model
            //--------------------------------------------
            $removeParams = $this->context['pool'];
            unset($removeParams['price']);


            list($originMin, $originMax) = self::getMinMax($this->context['unfilteredBoxes']);

            if ($originMin !== $originMax) {

                $currentMin = $originMin;
                $currentMax = $originMax;

//                        $formTrail = HiddenFormFieldsHelper::toFormFields($context['pool'], ['price']);

                if (null !== $this->userMin) {
                    $currentMin = $this->userMin;
                }
                if (null !== $this->userMax) {
                    $currentMax = $this->userMax;
                }


                $this->model = [
                    "title" => "Prix",
                    "uriReset" => UriTool::uri(null, $removeParams),
                    "name" => "price",
                    "type" => "minMax",
                    "minValue" => (int)$this->formatPrice($originMin),
                    "maxValue" => (int)$this->formatPrice($originMax),
                    "currentMin" => (int)$this->formatPrice($currentMin),
                    "currentMax" => (int)$this->formatPrice($currentMax),
                    "moneyFormatArgs" => E::conf("moneyFormatArgs"),
                ];
            }
        }
        return $this->model;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getMinMax(array $boxes)
    {
        $prices = [];
        if ($boxes) {
            foreach ($boxes as $box) {
                if (array_key_exists('priceSaleRaw', $box)) {
                    $prices[] = $box['priceSaleRaw'];
                }
            }

            return [
                min($prices),
                max($prices),
            ];
        }
        return [0, 0];
    }

    private function formatPrice($n)
    {
        return sprintf('%.2f', $n);
    }

}