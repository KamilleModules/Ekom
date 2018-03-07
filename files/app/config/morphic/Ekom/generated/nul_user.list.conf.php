<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `nul_user` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "users",
    'table' => 'nul_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'nul_user',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'pass' => 'Pass',
        'avatar' => 'Avatar',
        'pseudo' => 'Pseudo',
        'active' => 'Active',
        'date_created' => 'Date created',
        'date_last_connexion' => 'Date last connexion',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.email',
        'h.pass',
        'h.avatar',
        'h.pseudo',
        'h.active',
        'h.date_created',
        'h.date_last_connexion',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_NulUser_List",    
    'context' => $context,
];


