<?php


namespace Module\Ekom\HybridList\HybridListControl\Filter;


use Bat\UriTool;
use HybridList\HybridListControl\HybridListControl;
use HybridList\HybridListInterface;

class SummaryFilterHybridListControl extends HybridListControl
{

    /**
     * @var $items SummaryFilterAwareInterface[]
     */
    private $items;
    private $pool;


    public function __construct()
    {
        parent::__construct();
        $this->pool = null;
        $this->items = [];
    }

    public function addSummaryFilterAwareItem(SummaryFilterAwareInterface $summaryFilterAware)
    {
        $this->items[] = $summaryFilterAware;
        return $this;
    }

    public function prepareHybridList(HybridListInterface $list, array $context)
    {
        $this->pool = $context['pool'];
        return $this;
    }


    public function getModel()
    {
        if (empty($this->model)) {

            $pool = $this->pool;
            $uriParams = $pool;
            $summaryItems = [];

            foreach ($pool as $name => $value) {

                $isArr = true;
                if (!is_array($value)) {
                    $isArr = false;
                    $value = [$value];
                }


                foreach ($this->items as $item) {
                    foreach ($value as $val) {


                        $thisUriParams = $uriParams;


                        $label = $item->getSummaryFilterItem($name, $val);

                        if (null !== $label) {

                            if (true === $isArr) {
                                if (false !== ($index = array_search($val, $thisUriParams[$name]))) {
                                    unset($thisUriParams[$name][$index]);
                                }
                            } else {
                                unset($thisUriParams[$name]);
                            }

                            $summaryItems[$name][] = [
                                'label' => $label,
                                'uri' => UriTool::uri(null, $thisUriParams),
                            ];
                        }
                    }
                }

            }


            $this->model = [
                'items' => $summaryItems,
                'uriClearAll' => UriTool::uri(),
            ];
        }
        return $this->model;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getIdentifier($name, $value)
    {
        return $name . $value;
    }
}