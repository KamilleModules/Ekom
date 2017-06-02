<?php




$prc = "Ekom.kamille.ek_feature_value";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_feature_value.id',
            'ek_feature_value.value',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_feature_value.lang_id',
        ],
        'ric' => [
            'ek_feature_value.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_feature_value',
            ],
        ],
    ],
]);
