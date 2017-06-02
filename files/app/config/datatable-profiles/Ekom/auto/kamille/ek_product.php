<?php




$prc = "Ekom.kamille.ek_product";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product.id',
            'ek_product_reference.natural_reference',
            'action',
        ],
        'hidden' => [
            'ek_product.product_reference_id',
        ],
        'ric' => [
            'ek_product.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product',
            ],
        ],
    ],
]);
