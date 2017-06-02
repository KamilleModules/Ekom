<?php




$prc = "Ekom.kamille.ek_product_reference_shop_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_reference_shop_lang.product_reference_shop_id',
            'ek_product_reference_shop_lang.label',
            'ek_product_reference_shop_lang.description',
            'ek_product_reference_shop.image',
            'action',
        ],
        'hidden' => [
            'ek_product_reference_shop_lang.product_reference_shop_id',
        ],
        'ric' => [
            'ek_product_reference_shop_lang.product_reference_shop_id',
            'ek_product_reference_shop_lang.label',
            'ek_product_reference_shop_lang.description',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_reference_shop_lang',
            ],
        ],
    ],
]);
