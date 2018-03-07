<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_user`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Users",
    'table' => 'ek_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'pass' => 'Pass',
        'pseudo' => 'Pseudo',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'date_creation' => 'Date creation',
        'mobile' => 'Mobile',
        'phone' => 'Phone',
        'phone_prefix' => 'Phone prefix',
        'newsletter' => 'Newsletter',
        'gender' => 'Gender',
        'birthday' => 'Birthday',
        'active_hash' => 'Active hash',
        'active' => 'Active',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'email',
        'pass',
        'pseudo',
        'first_name',
        'last_name',
        'date_creation',
        'mobile',
        'phone',
        'phone_prefix',
        'newsletter',
        'gender',
        'birthday',
        'active_hash',
        'active',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUser_List",    
];


