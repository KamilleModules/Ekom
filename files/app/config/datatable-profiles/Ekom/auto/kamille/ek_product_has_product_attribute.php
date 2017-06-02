<?php




$prc = "Ekom.kamille.ek_product_has_product_attribute";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_has_product_attribute.product_id',
            'ek_product_has_product_attribute.product_attribute_id',
            'ek_product.product_reference_id',
            'ek_product_attribute.label',
            'ek_product_attibute_value.label',
            'action',
        ],
        'hidden' => [
            'ek_product_has_product_attribute.product_id',
            'ek_product_has_product_attribute.product_attribute_id',
            'ek_product_has_product_attribute.product_attibute_value_id',
        ],
        'ric' => [
            'ek_product_has_product_attribute.product_id',
            'ek_product_has_product_attribute.product_attribute_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_has_product_attribute',
            ],
        ],
    ],
]);
