<?php




$prc = "Ekom.kamille.ek_shop_has_product";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_shop_has_product.shop_id',
            'ek_shop_has_product.product_id',
            'ek_shop.label',
            'ek_product.product_reference_id',
            'ek_shop_has_product.active',
            'action',
        ],
        'hidden' => [
            'ek_shop_has_product.shop_id',
            'ek_shop_has_product.product_id',
        ],
        'ric' => [
            'ek_shop_has_product.shop_id',
            'ek_shop_has_product.product_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_shop_has_product',
            ],
        ],
    ],
]);
