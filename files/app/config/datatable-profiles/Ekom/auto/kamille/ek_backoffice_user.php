<?php




$prc = "Ekom.kamille.ek_backoffice_user";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_backoffice_user.id',
            'ek_backoffice_user.email',
            'ek_backoffice_user.pass',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_backoffice_user.lang_id',
        ],
        'ric' => [
            'ek_backoffice_user.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_backoffice_user',
            ],
        ],
    ],
]);
