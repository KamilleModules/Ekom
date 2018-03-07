<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_discount`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Discounts",
    'table' => 'ek_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_discount',
    "headers" => [
        'id' => 'Id',
        'type' => 'Type',
        'operand' => 'Operand',
        'target' => 'Target',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'type',
        'operand',
        'target',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkDiscount_List",    
];


