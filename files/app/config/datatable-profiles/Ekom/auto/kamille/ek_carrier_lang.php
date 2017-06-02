<?php




$prc = "Ekom.kamille.ek_carrier_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_carrier_lang.id',
            'ek_carrier_lang.label',
            'ek_carrier_lang.description',
            'ek_carrier.id',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_carrier_lang.carrier_id',
            'ek_carrier_lang.lang_id',
        ],
        'ric' => [
            'ek_carrier_lang.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_carrier_lang',
            ],
        ],
    ],
]);
