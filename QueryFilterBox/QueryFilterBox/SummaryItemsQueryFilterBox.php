<?php


namespace Module\Ekom\QueryFilterBox\QueryFilterBox;


use Bat\UriTool;
use Module\Ekom\QueryFilterBox\CategoryAwareQueryFilterBoxInterface;
use QueryFilterBox\Collectable\CollectableInterface;
use QueryFilterBox\QueryFilterBox\QueryFilterBox;

class SummaryItemsQueryFilterBox extends QueryFilterBox implements CategoryAwareQueryFilterBoxInterface
{

    private $categoryId;

    /**
     * @var CollectableInterface[]
     */
    private $collectables;


    public function __construct()
    {
        parent::__construct();
        $this->categoryId = null;
        $this->collectables = [];
    }

    public static function create()
    {
        return new static();
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }


    public function prepare()
    {
        $summaryItems = [];
        $usedPool = array_intersect_key($this->pool, array_flip($this->usedPool));
        $uriParams = $usedPool;

        foreach ($usedPool as $name => $value) {


            $value = $this->pool[$name];
            $isArr = true;
            if (!is_array($value)) {
                $isArr = false;
                $value = [$value];
            }


            foreach ($this->collectables as $collectable) {
                foreach ($value as $val) {


                    $thisUriParams = $uriParams;


                    $res = $collectable->collect($name, $val);
                    if (null !== $res) {

                        if (true === $isArr) {
                            if (false !== ($index = array_search($val, $thisUriParams[$name]))) {
                                unset($thisUriParams[$name][$index]);
                            }
                        } else {
                            unset($thisUriParams[$name]);
                        }

                        $summaryItems[$name][] = [
                            'keyLabel' => $res['keyLabel'],
                            'label' => $res['valueLabel'],
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


    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function addCollectable(CollectableInterface $collectable)
    {
        $this->collectables[] = $collectable;
        return $this;
    }

}