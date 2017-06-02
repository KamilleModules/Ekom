<?php




$prc = "Ekom.kamille.ek_product_reference";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_reference.id',
            'ek_product_reference.natural_reference',
            'ek_product_reference.reference',
            'ek_product_reference.weight',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_product_reference.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_reference',
            ],
        ],
    ],
]);
