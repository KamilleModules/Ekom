<?php


use Module\Ekom\Back\User\EkomNullosUser;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from ek_user where shop_id=$shopId";

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Users",
    'table' => 'ek_user',
    'viewId' => 'user',
    'headers' => [
        'id' => "Id",
//        'shop_id' => 'Shop id',
        'email' => 'Email',
        'pseudo' => 'Pseudo',
        'first_name' => 'FirstName',
        'last_name' => 'LastName',
        'date_creation' => 'Creation date',
        'mobile' => 'Mobile',
        'phone_prefix' => 'Phone prefix',
        'phone' => 'Phone',
//        'newsletter' => 'newsletter',
        'gender' => 'Gender',
        'birthday' => 'Birthday',
        'active' => 'Active',
        '_action' => '',
    ],
    'querySkeleton' => $q,
    'queryCols' => [
        'id',
        'shop_id',
        'email',
        'pseudo',
        'first_name',
        'last_name',
        'date_creation',
        'mobile',
        'phone_prefix',
        'phone',
        'gender',
        'birthday',
        'active',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_User_List",
];