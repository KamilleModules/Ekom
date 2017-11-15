<?php


namespace Module\Ekom\HybridList\HybridListControl\Sort;


use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;
use Module\Ekom\HybridList\HybridListControl\HybridListControl;


/**
 *
 * The returned model is the sort component of a listBundle model
 * https://github.com/lingtalfi/Models/tree/master/ListBundle
 *
 */
class ProductSortHybridListControl extends HybridListControl
{

    private $attrNames;
    private $_alreadyReacted;
    private $_input;

    public function __construct()
    {
        parent::__construct();
        $this->attrNames = [];
        $this->_alreadyReacted = false;
        $this->_input = null;
    }

    public function prepareHybridList(HybridListInterface $list, array $context)
    {


        //--------------------------------------------
        // SHAPE REQUEST
        //--------------------------------------------
        $list
            ->addListShaper(ListShaper::create()
                ->reactsTo("sort")
                ->setExecuteCallback(function ($input, array &$boxes, array &$info = [], $originalBoxes) use ($context) {

                    $sortFn = null;
                    $this->_input = $input;
                    switch ($input) {
                        case "label_asc":
                            $sortFn = function ($boxA, $boxB) {
                                return $boxA['label_flat'] > $boxB["label_flat"];
                            };
                            break;
                        case "label_desc":
                            $sortFn = function ($boxA, $boxB) {
                                return $boxA['label_flat'] < $boxB["label_flat"];
                            };
                            break;
                        case "price_asc":
                            $sortFn = function ($boxA, $boxB) {
                                return $boxA['priceSaleRaw'] > $boxB["priceSaleRaw"];
                            };
                            break;
                        case "price_desc":
                            $sortFn = function ($boxA, $boxB) {
                                return $boxA['priceSaleRaw'] < $boxB["priceSaleRaw"];
                            };
                            break;
                        case "popularity":
                            $sortFn = function ($boxA, $boxB) {
                                return $boxA['popularity'] < $boxB["popularity"];
                            };
                            break;
                        default:
                            break;
                    }

                    if (null !== $sortFn) {
                        usort($boxes, $sortFn);
                    }


                })
            );
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getModel()
    {

        if (empty($this->model)) {
            $input = $this->_input;
            if (null === $input) {
                $input = 'label_asc'; // default sort
            }
            $this->model = [];
            $sorts = [
                'price_asc' => 'Par prix croissant',
                'price_desc' => 'Par prix décroissant',
                'label_asc' => 'De A à Z',
                'label_desc' => 'De Z à A',
                'popularity' => 'Par popularité',
            ];

            $items = [];
            foreach ($sorts as $value => $label) {
                $selected = ($value === $input);
                $items[] = [
                    "value" => $value,
                    "label" => $label,
                    "selected" => $selected,
                ];
            }
            $this->model = [
                'sortName' => "sort",
                'items' => $items,
            ];

        }
        return $this->model;

    }

}