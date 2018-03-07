<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `fm_mail_link` h
inner join fm_mail `m` on `m`.id=h.mail_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "mail links",
    'table' => 'fm_mail_link',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail_link',
    "headers" => [
        'id' => 'Id',
        'mail_id' => 'Mail id',
        'link_name' => 'Link name',
        'route' => 'Route',
        'route_params' => 'Route params',
        'hash' => 'Hash',
        'mail' => 'Mail',
        '_action' => '',
    ],
    "headersVisibility" => [
        'mail_id' => false,
    ],
    "realColumnMap" => [
        'mail' => [
            'm.id',
            'm.type',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.mail_id',
        'h.link_name',
        'h.route',
        'h.route_params',
        'h.hash',
        'concat( m.id, ". ", m.type ) as `mail`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMailLink_List",    
    'context' => $context,
];


