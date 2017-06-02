<?php




$prc = "Ekom.kamille.ek_tax_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_tax_lang.id',
            'ek_tax_lang.label',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_tax_lang.lang_id',
        ],
        'ric' => [
            'ek_tax_lang.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_tax_lang',
            ],
        ],
    ],
]);
