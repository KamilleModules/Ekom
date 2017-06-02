<?php




$prc = "Ekom.kamille.ek_product_reference_shop";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_reference_shop.id',
            'ek_product_reference_shop.image',
            'ek_product_reference_shop.prix_ht',
            'ek_shop.label',
            'ek_product_reference.natural_reference',
            'action',
        ],
        'hidden' => [
            'ek_product_reference_shop.shop_id',
            'ek_product_reference_shop.product_reference_id',
        ],
        'ric' => [
            'ek_product_reference_shop.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_reference_shop',
            ],
        ],
    ],
]);
