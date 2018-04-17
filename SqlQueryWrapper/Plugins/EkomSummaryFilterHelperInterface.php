<?php


namespace Module\Ekom\SqlQueryWrapper\Plugins;


interface EkomSummaryFilterHelperInterface
{


    /**
     * @return string|null, the label corresponding to the given paramName and paramValue (if it matches),
     *                  this will be used in the summary filter widget,
     *                  or null if the param doesn't match.
     */
    public function getSummaryItemLabel(string $paramName, $paramValue);


}