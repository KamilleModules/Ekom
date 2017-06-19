<?php


namespace Module\Ekom\ProductSearch;


use QuickPdo\QuickPdo;

class ProductSearch implements ProductSearchInterface
{

    /**
     * @param $query
     * @return array, each entry being an entry with the following keys:
     *          - value: the label to display
     *          - data: the uri to the product or product card
     *
     */
    public function getResults($query = ""){


        QuickPdo::fetchAll("
select         
        ");


    }
}

