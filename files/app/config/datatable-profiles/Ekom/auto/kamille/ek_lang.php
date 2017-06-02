<?php




$prc = "Ekom.kamille.ek_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_lang.id',
            'ek_lang.label',
            'ek_lang.iso_code',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_lang.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_lang',
            ],
        ],
    ],
]);
