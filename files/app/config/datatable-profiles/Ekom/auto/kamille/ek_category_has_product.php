<?php




$prc = "Ekom.kamille.ek_category_has_product";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_category_has_product.category_id',
            'ek_category_has_product.product_id',
            'ek_category.label',
            'ek_product.product_reference_id',
            'ek_category_has_product.order',
            'action',
        ],
        'hidden' => [
            'ek_category_has_product.category_id',
            'ek_category_has_product.product_id',
        ],
        'ric' => [
            'ek_category_has_product.category_id',
            'ek_category_has_product.product_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_category_has_product',
            ],
        ],
    ],
]);
