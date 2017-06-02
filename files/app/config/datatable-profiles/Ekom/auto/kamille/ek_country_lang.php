<?php




$prc = "Ekom.kamille.ek_country_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_country_lang.country_id',
            'ek_country_lang.lang_id',
            'ek_country_lang.label',
            'ek_country.iso_code',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_country_lang.country_id',
            'ek_country_lang.lang_id',
        ],
        'ric' => [
            'ek_country_lang.country_id',
            'ek_country_lang.lang_id',
            'ek_country_lang.label',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_country_lang',
            ],
        ],
    ],
]);
