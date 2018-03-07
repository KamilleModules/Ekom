<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // productId
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$shopId = EkomNullosUser::getEkomValue("shop_id");
$langId = EkomNullosUser::getEkomValue("lang_id");



$q = "
select %s 
from ek_shop_has_product_has_tag h 
inner join ek_shop_has_product_lang hl on hl.shop_id=h.shop_id and hl.product_id=h.product_id
inner join ek_shop_has_product sh on sh.shop_id=h.shop_id and sh.product_id=h.product_id
inner join ek_product p on p.id=h.product_id
inner join ek_tag t on t.id=h.tag_id
where h.shop_id=$shopId    
and h.product_id=$id
and t.lang_id=$langId
and hl.lang_id=$langId
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Tags for product #$avatar",
    'table' => 'ek_shop_has_product_has_tag',
    'viewId' => 'shop_has_product_has_tag',
    'queryCols' => [
        'h.product_id',
        'h.tag_id',
        'concat (p.id, ". ", hl.label, " (ref=",
case when sh.reference != ""
then sh.reference
else
p.reference
end,
")"       
        ) as product',
        'concat(t.id, ". ", t.name) as tag',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'product_id' => "Product id",
        'tag_id' => "Tag id",
        'product' => "Product",
        'tag' => "Tag",
        '_action' => '',
    ],
    'headersVisibility' => [
        'product_id' => false,
        'tag_id' => false,
    ],
    'realColumnMap' => [
        'product' => [
            'hl.label',
            'hl.slug',
            'p.reference',
            'p.id',
        ],
        'tag' => 't.name',
    ],
    'ric' => [
        'product_id',
        'tag_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ShopHasProductTag_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


