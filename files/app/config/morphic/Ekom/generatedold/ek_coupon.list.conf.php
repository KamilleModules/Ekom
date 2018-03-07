<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_coupon`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Coupons",
    'table' => 'ek_coupon',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_coupon',
    "headers" => [
        'id' => 'Id',
        'code' => 'Code',
        'active' => 'Active',
        'procedure_type' => 'Procedure type',
        'procedure_operand' => 'Procedure operand',
        'target' => 'Target',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'code',
        'active',
        'procedure_type',
        'procedure_operand',
        'target',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCoupon_List",    
];


