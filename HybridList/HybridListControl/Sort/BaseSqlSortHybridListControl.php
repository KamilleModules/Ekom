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
abstract class BaseSqlSortHybridListControl extends HybridListControl
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


    abstract protected function decorateSqlRequestByInput($input, SqlRequestInterface $r);

    abstract protected function getSort2Label();
    abstract protected function getDefaultSort();




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
                    $this->decorateSqlRequestByInput($input, $r);
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
                $input = $this->getDefaultSort(); // default sort
            }
            $this->model = [];
            $sorts = $this->getSort2Label();

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