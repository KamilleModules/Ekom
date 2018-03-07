<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_event_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ekev_event `e` on `e`.id=h.event_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "event langs",
    'table' => 'ekev_event_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_event_lang',
    "headers" => [
        'event_id' => 'Event id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'lang' => 'Lang',
        'event' => 'Event',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'event_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'event' => [
            'e.id',
            'e.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.event_id',
        'h.lang_id',
        'h.label',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( e.id, ". ", e.name ) as `event`',
    ],
    "ric" => [
        'event_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevEventLang_List",    
    'context' => $context,
];


