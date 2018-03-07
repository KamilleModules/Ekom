<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_shop_has_product_card_lang`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has product card langs",
    'table' => 'ek_shop_has_product_card_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_card_lang',
    "headers" => [
        'product_card_id' => 'Product card id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'slug' => 'Slug',
        'description' => 'Description',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'shop_id',
        'product_card_id',
        'lang_id',
        'label',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ],
    "ric" => [
        'product_card_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List",    
];


