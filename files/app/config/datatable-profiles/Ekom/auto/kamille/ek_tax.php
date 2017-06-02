<?php




$prc = "Ekom.kamille.ek_tax";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_tax.id',
            'ek_tax.reduction',
            'ek_tax_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_tax.tax_lang_id',
        ],
        'ric' => [
            'ek_tax.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_tax',
            ],
        ],
    ],
]);
