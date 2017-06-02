<?php




$prc = "Ekom.kamille.ek_product_attribute";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_attribute.id',
            'ek_product_attribute.label',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_product_attribute.lang_id',
        ],
        'ric' => [
            'ek_product_attribute.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_attribute',
            ],
        ],
    ],
]);
