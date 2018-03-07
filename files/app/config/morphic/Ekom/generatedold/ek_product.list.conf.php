<?php 





$q = "select %s from `ek_product`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Products",
    'table' => 'ek_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product',
    "headers" => [
        'id' => 'Id',
        'reference' => 'Reference',
        'weight' => 'Weight',
        'price' => 'Price',
        'product_card_id' => 'Product card id',
        'width' => 'Width',
        'height' => 'Height',
        'depth' => 'Depth',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'reference',
        'weight',
        'price',
        'product_card_id',
        'width',
        'height',
        'depth',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProduct_List",    
];


