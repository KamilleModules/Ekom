<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `fm_mail_clicked` h
inner join fm_mail_link `m` on `m`.id=h.mail_link_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "mail clickeds",
    'table' => 'fm_mail_clicked',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail_clicked',
    "headers" => [
        'id' => 'Id',
        'mail_link_id' => 'Mail link id',
        'date_clicked' => 'Date clicked',
        'mail_link' => 'Mail link',
        '_action' => '',
    ],
    "headersVisibility" => [
        'mail_link_id' => false,
    ],
    "realColumnMap" => [
        'mail_link' => [
            'm.id',
            'm.link_name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.mail_link_id',
        'h.date_clicked',
        'concat( m.id, ". ", m.link_name ) as `mail_link`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMailClicked_List",    
    'context' => $context,
];


