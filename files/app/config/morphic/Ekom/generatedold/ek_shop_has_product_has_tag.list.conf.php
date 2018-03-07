<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_shop_has_product_has_tag` h 
inner join ek_shop_has_product s on s.shop_id=h.shop_id 
inner join ek_shop_has_product s on s.product_id=h.product_id 
inner join ek_tag t on t.id=h.tag_id
where ";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has product has tags",
    'table' => 'ek_shop_has_product_has_tag',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_has_tag',
    "headers" => [
        'product_id' => 'Product id',
        'tag_id' => 'Tag id',
        'shop_has_product' => 'Shop has product',
        'tag' => 'Tag',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'product_id' => false,
        'tag_id' => false,
    ],
    "realColumnMap" => [
        'tag' => [
            't.name',
            't.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_id',
        'h.tag_id',
        'concat(s._discount_badge) as shop_has_product',
        'concat(s._discount_badge) as shop_has_product',
        'concat(t.id, ". ", t.name) as tag',
    ],
    "ric" => [
        'product_id',
        'tag_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List",    
    'context' => $context,
];


