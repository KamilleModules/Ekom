<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$order_id = MorphicHelper::getFormContextValue("order_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_order_has_order_status` h 
inner join ek_order o on o.id=h.order_id 
inner join ek_order_status or on or.id=h.order_status_id
where h.order_id=$order_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Order has order statuses",
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
        'order_status' => 'Order status',
        '_action' => '',
    ],
    "headersVisibility" => [
        'order_id' => false,
        'order_status_id' => false,
    ],
    "realColumnMap" => [
        'order_status' => [
            'or.code',
            'or.color',
            'or.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.order_id',
        'h.order_status_id',
        'h.date',
        'h.extra',
        'concat(or.id, ". ", or.code) as order_status',
    ],
    "ric" => [
        'id',
    ],
    
    "formRouteExtraVars" => [               
        "order_id" => $order_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkOrderHasOrderStatus_List",    
    'context' => $context,
];


