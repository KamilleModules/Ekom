<?php




$prc = "Ekom.kamille.ek_comment";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_comment.id',
            'ek_user.email',
            'ek_shop.label',
            'ek_comment.text',
            'ek_comment.date_creation',
            'ek_comment.active',
            'action',
        ],
        'hidden' => [
            'ek_comment.user_id',
            'ek_comment.shop_id',
        ],
        'ric' => [
            'ek_comment.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_comment',
            ],
        ],
    ],
]);
