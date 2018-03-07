<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_invoice`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Invoices",
    'table' => 'ek_invoice',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_invoice',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'order_id' => 'Order id',
        'seller_id' => 'Seller id',
        'label' => 'Label',
        'invoice_number' => 'Invoice number',
        'invoice_number_alt' => 'Invoice number alt',
        'invoice_date' => 'Invoice date',
        'payment_method' => 'Payment method',
        'currency_iso_code' => 'Currency iso code',
        'lang_iso_code' => 'Lang iso code',
        'shop_host' => 'Shop host',
        'track_identifier' => 'Track identifier',
        'amount' => 'Amount',
        'seller' => 'Seller',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'user_id',
        'order_id',
        'seller_id',
        'label',
        'invoice_number',
        'invoice_number_alt',
        'invoice_date',
        'payment_method',
        'currency_iso_code',
        'lang_iso_code',
        'shop_host',
        'track_identifier',
        'amount',
        'seller',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkInvoice_List",    
];


