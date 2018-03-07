<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // productId
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$shopId = EkomNullosUser::getEkomValue("shop_id");



$q = "
select %s 
from ek_shop_has_product_lang h 
inner join ek_lang l on l.id=h.lang_id
where h.shop_id=$shopId    
and h.product_id=$id
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Translations for product #$avatar",
    'table' => 'ek_shop_has_product_lang',
    'viewId' => 'shop_has_product_lang',
    'queryCols' => [
        'h.product_id',
        'h.lang_id',
        'l.iso_code as lang',
        'h.label',
        'h.description',
        'h.slug',
        'h.out_of_stock_text',
        'h.meta_title',
        'h.meta_description',
        'h.meta_keywords',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'product_id' => "Product id",
        'lang_id' => "Lang id",
        'lang' => "Lang",
        'label' => "Label",
        'description' => "Description",
        'slug' => "Slug",
        'out_of_stock_text' => "Out of stock text",
        'meta_title' => "Meta title",
        'meta_description' => "Meta description",
        'meta_keywords' => "Meta keywords",
        '_action' => '',
    ],
    'headersVisibility' => [
        'product_id' => false,
        'lang_id' => false,
    ],
    'realColumnMap' => [
        'lang' => 'l.iso_code',
    ],
    'ric' => [
        'product_id',
        'lang_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ShopHasProductLang_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


