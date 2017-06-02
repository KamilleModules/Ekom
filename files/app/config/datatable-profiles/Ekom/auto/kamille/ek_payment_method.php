<?php




$prc = "Ekom.kamille.ek_payment_method";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_payment_method.id',
            'ek_payment_method.label',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_payment_method.lang_id',
        ],
        'ric' => [
            'ek_payment_method.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_payment_method',
            ],
        ],
    ],
]);
