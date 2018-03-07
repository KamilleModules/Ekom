<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_invoice` h
inner join ek_order `o` on `o`.id=h.order_id
inner join ek_seller `s` on `s`.id=h.seller_id
inner join ek_shop `sh` on `sh`.id=h.shop_id
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "invoices",
    'table' => 'ek_invoice',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_invoice',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
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
        'order' => 'Order',
        'shop' => 'Shop',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'order_id' => false,
        'seller_id' => false,
        'shop_id' => false,
        'user_id' => false,
    ],
    "realColumnMap" => [
        'order' => [
            'o.id',
            'o.reference',
        ],
        'seller' => [
            's.id',
            's.name',
        ],
        'shop' => [
            'sh.id',
            'sh.label',
        ],
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.shop_id',
        'h.user_id',
        'h.order_id',
        'h.seller_id',
        'h.label',
        'h.invoice_number',
        'h.invoice_number_alt',
        'h.invoice_date',
        'h.payment_method',
        'h.currency_iso_code',
        'h.lang_iso_code',
        'h.shop_host',
        'h.track_identifier',
        'h.amount',
        'h.seller',
        'concat( o.id, ". ", o.reference ) as `order`',
        'concat( s.id, ". ", s.name ) as `seller`',
        'concat( sh.id, ". ", sh.label ) as `shop`',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkInvoice_List",    
    'context' => $context,
];


