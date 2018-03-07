<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_card_id = MorphicHelper::getFormContextValue("product_card_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_product_card_has_tax_group` h 
inner join ek_product_card p on p.id=h.product_card_id 
inner join ek_tax_group t on t.id=h.tax_group_id 
inner join ek_shop s on s.id=h.shop_id
where h.product_card_id=$product_card_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product card has tax groups",
    'table' => 'ek_product_card_has_tax_group',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_card_has_tax_group',
    "headers" => [
        'product_card_id' => 'Product card id',
        'tax_group_id' => 'Tax group id',
        'tax_group' => 'Tax group',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_id' => false,
        'tax_group_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'tax_group' => [
            't.label',
            't.name',
            't.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_card_id',
        'h.tax_group_id',
        'concat(t.id, ". ", t.label) as tax_group',
        'concat(s.id, ". ", s.label) as shop',
    ],
    "ric" => [
        'product_card_id',
    ],
    
    "formRouteExtraVars" => [               
        "product_card_id" => $product_card_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductCardHasTaxGroup_List",    
    'context' => $context,
];


