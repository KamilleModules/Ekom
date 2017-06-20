<?php


namespace Module\Ekom\Utils;


class SearchFilter
{
    /**
     * @var array of filterName => filterValue
     *
     * All filterNames and filterValues are already sanitized.
     *
     */
    public $filters = [];

    function __toString()
    {
        $filters = $this->filters;
        ksort($filters);
        $s = '';
        $c = 0;
        foreach ($filters as $k => $v) {
            if (0 !== $c++) {
                $s .= ".";
            }
            /**
             * assuming filters are controlled by the dev.
             * Otherwise (if they come from the user),
             * we probably want to sanitize the value with noEscalating?
             *
             *
             */
            $s .= "$k-$v";
        }
        return $s;
    }


}
