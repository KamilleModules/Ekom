<?php


namespace Module\Ekom\HybridList\HybridListControl\Filter;


use Bat\UriTool;
use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;
use HybridList\RequestGenerator\RequestGeneratorInterface;
use HybridList\RequestShaper\RequestShaper;
use HybridList\SqlRequest\SqlRequestInterface;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\AttributeLayer;
use Module\Ekom\HybridList\HiddenFormFieldsHelper;
use Module\Ekom\HybridList\HybridListControl\HybridListControl;
use Module\Ekom\Utils\E;

class PriceFilterHybridListControl extends HybridListControl
{

    private $attrNames;
    private $_alreadyReacted;

    public function __construct()
    {
        parent::__construct();
        $this->attrNames = [];
        $this->_alreadyReacted = false;
    }

    public function prepareHybridList(HybridListInterface $list, array $context)
    {
        $this->_alreadyReacted = false;
        $this->attrNames = AttributeLayer::getAttributeNamesByShopId();

        $list
            ->addListShaper(ListShaper::create()
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
                            }
                        }
                    }

                    //--------------------------------------------
                    // prepare model
                    //--------------------------------------------
                    list($originMin, $originMax) = self::getMinMax($context['unfilteredBoxes']);

                    if ($originMin !== $originMax) {

                        $currentMin = $originMin;
                        $currentMax = $originMax;

//                        $formTrail = HiddenFormFieldsHelper::toFormFields($context['pool'], ['price']);

                        if (null !== $min) {
                            $currentMin = $min;
                        }
                        if (null !== $max) {
                            $currentMax = $max;
                        }


                        $this->model = [
                            "formTrail" => 0,
                            "title" => "Prix",
                            "name" => "price",
                            "type" => "minMax",
                            "minValue" => (int)$this->formatPrice($originMin),
                            "maxValue" => (int)$this->formatPrice($originMax),
                            "currentMin" => (int)$this->formatPrice($currentMin),
                            "currentMax" => (int)$this->formatPrice($currentMax),
                            "moneyFormatArgs" => E::conf("moneyFormatArgs"),
                        ];
                    }

                })
            );


        return $this;

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getMinMax(array $boxes)
    {
        $prices = [];
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

    private function formatPrice($n)
    {
        return sprintf('%.2f', $n);
    }

}