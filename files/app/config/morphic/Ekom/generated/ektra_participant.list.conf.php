<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_participant` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "participants",
    'table' => 'ektra_participant',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_participant',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'phone' => 'Phone',
        'email' => 'Email',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.user_id',
        'h.first_name',
        'h.last_name',
        'h.phone',
        'h.email',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraParticipant_List",    
    'context' => $context,
];


