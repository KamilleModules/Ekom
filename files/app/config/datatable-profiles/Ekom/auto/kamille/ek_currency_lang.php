<?php




$prc = "Ekom.kamille.ek_currency_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_currency_lang.currency_id',
            'ek_currency_lang.lang_id',
            'ek_currency_lang.name',
            'ek_currency.iso_code',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_currency_lang.currency_id',
            'ek_currency_lang.lang_id',
        ],
        'ric' => [
            'ek_currency_lang.currency_id',
            'ek_currency_lang.lang_id',
            'ek_currency_lang.name',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_currency_lang',
            ],
        ],
    ],
]);
