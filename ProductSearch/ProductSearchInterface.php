<?php


namespace Module\Ekom\ProductSearch;


interface ProductSearchInterface
{

    /**
     * @param $query
     * @return array, depends on the search engine...
     */
    public function getResults($query = "");
}

