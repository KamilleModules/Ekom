<?php


namespace Module\Ekom\HybridList\HybridListControl\Sort;


use HybridList\HybridListControl\HybridListControl;
use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;
use HybridList\RequestShaper\RequestShaper;
use HybridList\SqlRequest\SqlRequestInterface;


/**
 *
 * The returned model is the sort component of a listBundle model
 * https://github.com/lingtalfi/Models/tree/master/ListBundle
 *
 */
class OrderSortHybridListControl extends HybridListControl
{

    private $attrNames;
    private $_alreadyReacted;
    private $_input;
    private $sortName;

    public function __construct()
    {
        parent::__construct();
        $this->attrNames = [];
        $this->_alreadyReacted = false;
        $this->_input = null;
        $this->sortName = 'sort';
    }

    public function prepareHybridList(HybridListInterface $list, array $context)
    {

        //--------------------------------------------
        // SHAPE REQUEST
        //--------------------------------------------
        $list
            ->getRequestGenerator()->addRequestShaper(RequestShaper::create()
                ->reactsTo($this->sortName)
                ->setExecuteCallback(function ($input, SqlRequestInterface $r) use ($context) {


                    $sortFn = null;
                    $this->_input = $input;
                    switch ($input) {
                        case "amount_asc":
                            $r->addOrderBy("amount", "asc");
                            break;
                        case "amount_desc":
                            $r->addOrderBy("amount", "desc");
                            break;
                        case "date_asc":
                            $r->addOrderBy("date", "asc");
                            break;
                        case "date_desc":
                            $r->addOrderBy("date", "desc");
                            break;
                        case "ref_asc":
                            $r->addOrderBy("ref", "asc");
                            break;
                        case "ref_desc":
                            $r->addOrderBy("ref", "desc");
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

    public function setSortName($sortName)
    {
        $this->sortName = $sortName;
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
                'amount_asc' => 'Par montant croissant',
                'amount_desc' => 'Par montant décroissant',
                'date_asc' => 'Par date croissante',
                'date_desc' => 'Par date décroissante',
                'ref_asc' => 'Par référence croissante',
                'ref_desc' => 'Par référence décroissante',
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
                'sortName' => $this->sortName,
                'items' => $items,
            ];

        }
        return $this->model;

    }

}