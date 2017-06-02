<?php




$prc = "Ekom.kamille.ek_product_reference_store";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_reference_store.id',
            'ek_store.label',
            'ek_product_reference_store.quantity',
            'ek_product_reference.natural_reference',
            'action',
        ],
        'hidden' => [
            'ek_product_reference_store.store_id',
            'ek_product_reference_store.product_reference_id',
        ],
        'ric' => [
            'ek_product_reference_store.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_reference_store',
            ],
        ],
    ],
]);
