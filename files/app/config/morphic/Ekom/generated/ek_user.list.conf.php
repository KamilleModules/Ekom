<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_user` h
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "users",
    'table' => 'ek_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
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
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.shop_id',
        'h.email',
        'h.pass',
        'h.pseudo',
        'h.first_name',
        'h.last_name',
        'h.date_creation',
        'h.mobile',
        'h.phone',
        'h.phone_prefix',
        'h.newsletter',
        'h.gender',
        'h.birthday',
        'h.active_hash',
        'h.active',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUser_List",    
    'context' => $context,
];


