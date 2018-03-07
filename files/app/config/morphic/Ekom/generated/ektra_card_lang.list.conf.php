<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_card_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ektra_card `c` on `c`.id=h.training_card_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "card langs",
    'table' => 'ektra_card_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_card_lang',
    "headers" => [
        'training_card_id' => 'Training card id',
        'lang_id' => 'Lang id',
        'prerequisites' => 'Prerequisites',
        'lang' => 'Lang',
        'card' => 'Card',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'training_card_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'card' => [
            'c.id',
            'c.shop_id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.training_card_id',
        'h.lang_id',
        'h.prerequisites',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( c.id, ". ", c.shop_id ) as `card`',
    ],
    "ric" => [
        'training_card_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraCardLang_List",    
    'context' => $context,
];


