<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ekfs_product`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Ekfs products",
    'table' => 'ekfs_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekfs_product',
    "headers" => [
        'id' => 'Id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'ref' => 'Ref',
        'sale_price_without_tax' => 'Sale price without tax',
        'sale_price_with_tax' => 'Sale price with tax',
        'attr_string' => 'Attr string',
        'uri_card' => 'Uri card',
        'uri_thumb' => 'Uri thumb',
        'product_id' => 'Product id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'lang_id',
        'label',
        'ref',
        'sale_price_without_tax',
        'sale_price_with_tax',
        'attr_string',
        'uri_card',
        'uri_thumb',
        'product_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkfsProduct_List",    
];


