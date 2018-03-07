<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_backoffice_user` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "backoffice users",
    'table' => 'ek_backoffice_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_backoffice_user',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'shop_id' => 'Shop id',
        'lang_id' => 'Lang id',
        'currency_id' => 'Currency id',
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
        'h.shop_id',
        'h.lang_id',
        'h.currency_id',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkBackofficeUser_List",    
    'context' => $context,
];


