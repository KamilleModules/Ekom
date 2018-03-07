<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `pei_direct_debit` h
inner join ek_order `o` on `o`.id=h.order_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "direct debits",
    'table' => 'pei_direct_debit',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'pei_direct_debit',
    "headers" => [
        'id' => 'Id',
        'order_id' => 'Order id',
        'date' => 'Date',
        'paid' => 'Paid',
        'transaction_reference' => 'Transaction reference',
        'pay_id' => 'Pay id',
        'feedback_details' => 'Feedback details',
        'amount' => 'Amount',
        'currency' => 'Currency',
        'alias' => 'Alias',
        'shop_id' => 'Shop id',
        'order' => 'Order',
        '_action' => '',
    ],
    "headersVisibility" => [
        'order_id' => false,
    ],
    "realColumnMap" => [
        'order' => [
            'o.id',
            'o.reference',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.order_id',
        'h.date',
        'h.paid',
        'h.transaction_reference',
        'h.pay_id',
        'h.feedback_details',
        'h.amount',
        'h.currency',
        'h.alias',
        'h.shop_id',
        'concat( o.id, ". ", o.reference ) as `order`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_PeiDirectDebit_List",    
    'context' => $context,
];


