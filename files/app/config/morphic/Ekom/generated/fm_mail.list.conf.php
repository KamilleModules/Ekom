<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `fm_mail` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "mails",
    'table' => 'fm_mail',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail',
    "headers" => [
        'id' => 'Id',
        'type' => 'Type',
        'date_sent' => 'Date sent',
        'email' => 'Email',
        'hash' => 'Hash',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.type',
        'h.date_sent',
        'h.email',
        'h.hash',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMail_List",    
    'context' => $context,
];


