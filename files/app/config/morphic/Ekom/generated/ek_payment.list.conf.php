<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_payment` h
inner join ek_invoice `i` on `i`.id=h.invoice_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "payments",
    'table' => 'ek_payment',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_payment',
    "headers" => [
        'id' => 'Id',
        'invoice_id' => 'Invoice id',
        'date' => 'Date',
        'paid' => 'Paid',
        'feedback_details' => 'Feedback details',
        'amount' => 'Amount',
        'invoice' => 'Invoice',
        '_action' => '',
    ],
    "headersVisibility" => [
        'invoice_id' => false,
    ],
    "realColumnMap" => [
        'invoice' => [
            'i.id',
            'i.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.invoice_id',
        'h.date',
        'h.paid',
        'h.feedback_details',
        'h.amount',
        'concat( i.id, ". ", i.label ) as `invoice`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkPayment_List",    
    'context' => $context,
];


