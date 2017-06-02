<?php




$prc = "Ekom.kamille.ek_category";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_category.id',
            'ek_category.label',
            'ek_category.is_active',
            'ek_shop.label',
            'action',
        ],
        'hidden' => [
            'ek_category.shop_id',
            'ek_category.category_id',
        ],
        'ric' => [
            'ek_category.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_category',
            ],
        ],
    ],
]);
