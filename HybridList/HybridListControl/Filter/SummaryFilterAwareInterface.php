<?php


namespace Module\Ekom\HybridList\HybridListControl\Filter;


interface SummaryFilterAwareInterface
{


    /**
     * @param $param
     * @param $value
     * @return null|string, the label to use in the summary filter widget,
     *          or null if the param doesn't match.
     */
    public function getSummaryFilterItem($param, $value);
}