<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_shop_product_card_event` h
inner join ek_product_card `p` on `p`.id=h.product_card_id
inner join ek_shop `s` on `s`.id=h.shop_id
inner join ekev_event `e` on `e`.id=h.event_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop product card events",
    'table' => 'ekev_shop_product_card_event',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_shop_product_card_event',
    "headers" => [
        'shop_id' => 'Shop id',
        'event_id' => 'Event id',
        'product_card_id' => 'Product card id',
        'product_card' => 'Product card',
        'shop' => 'Shop',
        'event' => 'Event',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_id' => false,
        'shop_id' => false,
        'event_id' => false,
    ],
    "realColumnMap" => [
        'product_card' => [
            'p.id',
            'p.id',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
        'event' => [
            'e.id',
            'e.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.event_id',
        'h.product_card_id',
        'concat( p.id, ". ", p.id ) as `product_card`',
        'concat( s.id, ". ", s.label ) as `shop`',
        'concat( e.id, ". ", e.name ) as `event`',
    ],
    "ric" => [
        'shop_id',
        'event_id',
        'product_card_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List",    
    'context' => $context,
];


