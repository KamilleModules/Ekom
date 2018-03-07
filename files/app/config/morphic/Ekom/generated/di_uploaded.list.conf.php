<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `di_uploaded` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "uploadeds",
    'table' => 'di_uploaded',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_uploaded',
    "headers" => [
        'id' => 'Id',
        'path' => 'Path',
        'date_upload' => 'Date upload',
        'ip' => 'Ip',
        'http_user_agent' => 'Http user agent',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.path',
        'h.date_upload',
        'h.ip',
        'h.http_user_agent',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUploaded_List",    
    'context' => $context,
];


