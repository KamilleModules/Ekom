<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_card` h
inner join ek_product_card `p` on `p`.id=h.product_card_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "cards",
    'table' => 'ektra_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_card',
    "headers" => [
        'id' => 'Id',
        'product_card_id' => 'Product card id',
        'shop_id' => 'Shop id',
        'product_card' => 'Product card',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_id' => false,
        'shop_id' => false,
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
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.product_card_id',
        'h.shop_id',
        'concat( p.id, ". ", p.id ) as `product_card`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraCard_List",    
    'context' => $context,
];


