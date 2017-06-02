<?php




$prc = "Ekom.kamille.ek_product_origin";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_origin.id',
            'ek_product_origin.type',
            'ek_product_origin.value',
            'ek_product_origin.image',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_product_origin.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_origin',
            ],
        ],
    ],
]);
