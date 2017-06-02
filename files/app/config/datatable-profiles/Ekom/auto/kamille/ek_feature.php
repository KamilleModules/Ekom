<?php




$prc = "Ekom.kamille.ek_feature";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_feature.id',
            'ek_feature.label',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_feature.lang_id',
        ],
        'ric' => [
            'ek_feature.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_feature',
            ],
        ],
    ],
]);
