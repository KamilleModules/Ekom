<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_order_has_order_status` h
inner join ek_order `o` on `o`.id=h.order_id
inner join ek_order_status `or` on `or`.id=h.order_status_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "order-order statuses",
    'table' => 'ek_order_has_order_status',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_order_has_order_status',
    "headers" => [
        'id' => 'Id',
        'order_id' => 'Order id',
        'order_status_id' => 'Order status id',
        'date' => 'Date',
        'extra' => 'Extra',
        'order' => 'Order',
        'order_status' => 'Order status',
        '_action' => '',
    ],
    "headersVisibility" => [
        'order_id' => false,
        'order_status_id' => false,
    ],
    "realColumnMap" => [
        'order' => [
            'o.id',
            'o.reference',
        ],
        'order_status' => [
            'or.id',
            'or.code',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.order_id',
        'h.order_status_id',
        'h.date',
        'h.extra',
        'concat( o.id, ". ", o.reference ) as `order`',
        'concat( or.id, ". ", or.code ) as `order_status`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkOrderHasOrderStatus_List",    
    'context' => $context,
];


