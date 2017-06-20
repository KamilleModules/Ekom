<?php


namespace Module\Ekom\Utils;


class ListModifiers
{

    private $searchFilters = [];
    private $sortPredicates = [];


    public function setSearchFilter($key, $value)
    {
        $this->searchFilters[$key] = $value;
        return $this;
    }

    public function setSortPredicate($key, $value)
    {
        $this->sortPredicates[$key] = $value;
        return $this;
    }


    public function __toString()
    {
        $filters = $this->searchFilters;
        ksort($filters);
        $predicates = $this->sortPredicates;
        ksort($predicates);

        $s = '';
        $c = 0;
        $this->concat($s, $filters, $c);
        $this->concat($s, $predicates, $c);

        return $s;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function concat(&$s, array $arr, &$c)
    {
        foreach ($arr as $k => $v) {
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
    }

}
