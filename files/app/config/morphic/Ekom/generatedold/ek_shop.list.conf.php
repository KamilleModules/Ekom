<?php 





$q = "select %s from `ek_shop`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shops",
    'table' => 'ek_shop',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop',
    "headers" => [
        'id' => 'Id',
        'label' => 'Label',
        'host' => 'Host',
        'lang_id' => 'Lang id',
        'currency_id' => 'Currency id',
        'base_currency_id' => 'Base currency id',
        'timezone_id' => 'Timezone id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'label',
        'host',
        'lang_id',
        'currency_id',
        'base_currency_id',
        'timezone_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShop_List",    
];


