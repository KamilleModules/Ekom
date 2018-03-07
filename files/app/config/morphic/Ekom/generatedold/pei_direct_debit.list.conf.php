<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `pei_direct_debit`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Direct debits",
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
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'order_id',
        'date',
        'paid',
        'transaction_reference',
        'pay_id',
        'feedback_details',
        'amount',
        'currency',
        'alias',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_PeiDirectDebit_List",    
];


