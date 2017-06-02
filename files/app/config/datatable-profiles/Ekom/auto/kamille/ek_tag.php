<?php




$prc = "Ekom.kamille.ek_tag";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_tag.id',
            'ek_tag.label',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_tag.lang_id',
        ],
        'ric' => [
            'ek_tag.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_tag',
            ],
        ],
    ],
]);
