<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_backoffice_user`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Backoffice users",
    'table' => 'ek_backoffice_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_backoffice_user',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'lang_id' => 'Lang id',
        'currency_id' => 'Currency id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'email',
        'shop_id',
        'lang_id',
        'currency_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkBackofficeUser_List",    
];


