<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_cart_discount`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Cart discounts",
    'table' => 'ek_cart_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_cart_discount',
    "headers" => [
        'id' => 'Id',
        'target' => 'Target',
        'procedure_type' => 'Procedure type',
        'procedure_operand' => 'Procedure operand',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'target',
        'procedure_type',
        'procedure_operand',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCartDiscount_List",    
];


