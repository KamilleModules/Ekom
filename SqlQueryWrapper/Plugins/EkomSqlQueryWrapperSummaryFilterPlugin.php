<?php


namespace Module\Ekom\SqlQueryWrapper\Plugins;


use Bat\UriTool;
use SqlQueryWrapper\Plugins\SqlQueryWrapperBasePlugin;

/**
 * This plugin only honors one part of the deal: the view part.
 * It actually doesn't interact with the SqlQuery, and so I almost didn't make it as a plugin,
 * but since it works along with other true plugin filters,
 * and since it does half the job, it's easy to say that it's a plugin like the others.
 *
 * Now easy is not always bad (I hope), so...
 */
class EkomSqlQueryWrapperSummaryFilterPlugin extends SqlQueryWrapperBasePlugin
{

    /**
     * @var EkomSummaryFilterHelperInterface[]
     */
    protected $summaryItems;

    public function __construct()
    {
        parent::__construct();
        $this->summaryItems = [];
    }


    public function prepareModel(int $nbItems, array $rows)
    {
        $pool = $_GET;
        $uriParams = $pool;
        $summaryItems = [];

        foreach ($pool as $name => $value) {

            $isArr = true;
            if (!is_array($value)) {
                $isArr = false;
                $value = [$value];
            }


            foreach ($this->summaryItems as $item) {
                foreach ($value as $val) {


                    $thisUriParams = $uriParams;


                    $label = $item->getSummaryItemLabel($name, $val);

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


    public function addSummaryItem(EkomSummaryFilterHelperInterface $summaryItem)
    {
        $this->summaryItems[] = $summaryItem;
        return $this;
    }

}

