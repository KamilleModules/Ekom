<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_newsletter` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "newsletters",
    'table' => 'ek_newsletter',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_newsletter',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'subscribe_date' => 'Subscribe date',
        'unsubscribe_date' => 'Unsubscribe date',
        'active' => 'Active',
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
        'h.subscribe_date',
        'h.unsubscribe_date',
        'h.active',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkNewsletter_List",    
    'context' => $context,
];


