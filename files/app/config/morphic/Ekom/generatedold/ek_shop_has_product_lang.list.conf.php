<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_shop_has_product_lang`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has product langs",
    'table' => 'ek_shop_has_product_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_lang',
    "headers" => [
        'product_id' => 'Product id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'description' => 'Description',
        'slug' => 'Slug',
        'out_of_stock_text' => 'Out of stock text',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'shop_id',
        'product_id',
        'lang_id',
        'label',
        'description',
        'slug',
        'out_of_stock_text',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ],
    "ric" => [
        'lang_id',
        'product_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductLang_List",    
];


