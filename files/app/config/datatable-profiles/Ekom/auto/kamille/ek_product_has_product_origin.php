<?php




$prc = "Ekom.kamille.ek_product_has_product_origin";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_has_product_origin.product_id',
            'ek_product_has_product_origin.product_origin_id',
            'ek_product.product_reference_id',
            'ek_product_origin.type',
            'action',
        ],
        'hidden' => [
            'ek_product_has_product_origin.product_id',
            'ek_product_has_product_origin.product_origin_id',
        ],
        'ric' => [
            'ek_product_has_product_origin.product_id',
            'ek_product_has_product_origin.product_origin_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_has_product_origin',
            ],
        ],
    ],
]);
