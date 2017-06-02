<?php




$prc = "Ekom.kamille.ek_product_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_lang.product_id',
            'ek_product_lang.shop_id',
            'ek_product.product_reference_id',
            'ek_shop.label',
            'ek_product_lang.label',
            'ek_product_lang.description',
            'ek_product_lang.slug',
            'action',
        ],
        'hidden' => [
            'ek_product_lang.product_id',
            'ek_product_lang.shop_id',
        ],
        'ric' => [
            'ek_product_lang.product_id',
            'ek_product_lang.shop_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_lang',
            ],
        ],
    ],
]);
