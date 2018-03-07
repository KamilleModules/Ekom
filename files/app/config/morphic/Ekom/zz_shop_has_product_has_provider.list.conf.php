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
from ek_shop_has_product_has_provider h 
inner join ek_shop_has_product_lang hl on hl.shop_id=h.shop_id and hl.product_id=h.product_id
inner join ek_shop_has_product sh on sh.shop_id=h.shop_id and sh.product_id=h.product_id
inner join ek_product p on p.id=h.product_id
inner join ek_provider t on t.id=h.provider_id
where h.shop_id=$shopId    
and h.product_id=$id
and hl.lang_id=$langId
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Providers for product #$avatar",
    'table' => 'ek_shop_has_product_has_provider',
    'viewId' => 'shop_has_product_has_provider',
    'queryCols' => [
        'h.product_id',
        'h.provider_id',
        'concat (p.id, ". ", hl.label, " (ref=",
case when sh.reference != ""
then sh.reference
else
p.reference
end,
")"       
        ) as product',
        'concat(t.id, ". ", t.name) as provider',
        'h.wholesale_price',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'product_id' => "Product id",
        'provider_id' => "Provider id",
        'product' => "Product",
        'provider' => "Provider",
        'wholesale_price' => "Wholesale price",
        '_action' => '',
    ],
    'headersVisibility' => [
        'product_id' => false,
        'provider_id' => false,
    ],
    'realColumnMap' => [
        'product' => [
            'hl.label',
            'hl.slug',
            'p.reference',
            'p.id',
        ],
        'provider' => 't.name',
    ],
    'ric' => [
        'product_id',
        'provider_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ShopHasProductProvider_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


