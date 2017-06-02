<?php




$prc = "Ekom.kamille.ek_product_has_feature";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_has_feature.product_id',
            'ek_product_has_feature.feature_id',
            'ek_product.product_reference_id',
            'ek_feature.label',
            'ek_feature_value.value',
            'action',
        ],
        'hidden' => [
            'ek_product_has_feature.product_id',
            'ek_product_has_feature.feature_id',
            'ek_product_has_feature.feature_value_id',
        ],
        'ric' => [
            'ek_product_has_feature.product_id',
            'ek_product_has_feature.feature_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_has_feature',
            ],
        ],
    ],
]);
