<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
//--------------------------------------------
// foreach context keys
//--------------------------------------------
$seller_id = MorphicHelper::getFormContextValue("seller_id", $context);
// endforeach
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


/**
 * Note: I've dropped the country for the automation tool.
 * (no time to implement a second fkey level logic for now)
 * You should re-add it by hand after.
 *
 */

$q = "
select %s 
from ek_seller_has_address h
 
inner join ek_address a on a.id=h.address_id


where h.seller_id=$seller_id

";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Addresses of seller \"$avatar\"",
    'table' => 'ek_seller_has_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'test/test_has',
    'queryCols' => [
        'h.seller_id',
        'h.address_id',
        'h.order',
        'concat (
a.last_name,
" ",
a.first_name,
", ",
a.address,
" ",
a.city,
" ",
a.postcode
        ) as address',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'seller_id' => "Seller id",
        'address_id' => "Address id",
        'order' => "Order",
        'address' => "Address",
        '_action' => '',
    ],
    'headersVisibility' => [
        //--------------------------------------------
        // foreach context keys
        //--------------------------------------------
        'seller_id' => false,
        // endforeach
    ],
    'realColumnMap' => [
        'address' => [
            "a.id", // remember, in this array form, the first entry is used for order
            "a.first_name",
            "a.last_name",
            "a.address",
            "a.city",
            "a.postcode",
        ],
    ],
    'ric' => [
        'seller_id',
        'address_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_TestHas_List",
//    'formRouteExtraVars' => [
//        "id" => $id,
//    ],
    'context' => $context,
];


