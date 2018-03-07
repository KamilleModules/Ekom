<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `fm_mail_opened` h
inner join fm_mail `m` on `m`.id=h.mail_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "mail openeds",
    'table' => 'fm_mail_opened',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail_opened',
    "headers" => [
        'id' => 'Id',
        'mail_id' => 'Mail id',
        'date_opened' => 'Date opened',
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
        'h.date_opened',
        'concat( m.id, ". ", m.type ) as `mail`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMailOpened_List",    
    'context' => $context,
];


