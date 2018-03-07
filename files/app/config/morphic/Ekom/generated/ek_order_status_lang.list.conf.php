<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_order_status_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_order_status `o` on `o`.id=h.order_status_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "order status langs",
    'table' => 'ek_order_status_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_order_status_lang',
    "headers" => [
        'order_status_id' => 'Order status id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'lang' => 'Lang',
        'order_status' => 'Order status',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'order_status_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'order_status' => [
            'o.id',
            'o.code',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.order_status_id',
        'h.lang_id',
        'h.label',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( o.id, ". ", o.code ) as `order_status`',
    ],
    "ric" => [
        'order_status_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkOrderStatusLang_List",    
    'context' => $context,
];


