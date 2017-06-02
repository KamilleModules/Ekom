<?php




$prc = "Ekom.kamille.ek_product_attibute_value";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_attibute_value.id',
            'ek_product_attibute_value.label',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_product_attibute_value.lang_id',
        ],
        'ric' => [
            'ek_product_attibute_value.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_attibute_value',
            ],
        ],
    ],
]);
