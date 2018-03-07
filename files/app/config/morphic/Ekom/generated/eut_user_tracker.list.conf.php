<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `eut_user_tracker` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user trackers",
    'table' => 'eut_user_tracker',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'eut_user_tracker',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'date' => 'Date',
        'host' => 'Host',
        'route' => 'Route',
        'ip' => 'Ip',
        'https' => 'Https',
        'http_referer' => 'Http referer',
        'uri' => 'Uri',
        'get' => 'Get',
        'post' => 'Post',
        'http_user_agent' => 'Http user agent',
        'http_accept_language' => 'Http accept language',
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
        'h.date',
        'h.host',
        'h.route',
        'h.ip',
        'h.https',
        'h.http_referer',
        'h.uri',
        'h.get',
        'h.post',
        'h.http_user_agent',
        'h.http_accept_language',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EutUserTracker_List",    
    'context' => $context,
];


