<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_shop_has_product_card` h 
inner join ek_product_card p on p.id=h.product_card_id 
inner join ek_shop s on s.id=h.shop_id 
inner join ek_product pr on pr.id=h.product_id 
inner join ek_tax_group t on t.id=h.tax_group_id
where h.shop_id=$shop_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has product cards",
    'table' => 'ek_shop_has_product_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_card',
    "headers" => [
        'product_card_id' => 'Product card id',
        'product_id' => 'Product id',
        'tax_group_id' => 'Tax group id',
        'active' => 'Active',
        'product_card' => 'Product card',
        'product' => 'Product',
        'tax_group' => 'Tax group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'product_card_id' => false,
        'product_id' => false,
        'tax_group_id' => false,
    ],
    "realColumnMap" => [
        'product_card' => [
            'p.id',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_card_id',
        'h.product_id',
        'h.tax_group_id',
        'h.active',
        'concat(p.id, ". ", p.id) as product_card',
        'concat(pr.id, ". ", pr.reference) as product',
        'concat(t.id, ". ", t.label) as tax_group',
    ],
    "ric" => [
        'product_card_id',
    ],
    
    "formRouteExtraVars" => [               
        "shop_id" => $shop_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductCard_List",    
    'context' => $context,
];


