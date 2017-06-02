<?php




$prc = "Ekom.kamille.ek_shop_has_store";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_shop_has_store.shop_id',
            'ek_shop_has_store.store_id',
            'ek_shop.label',
            'ek_store.label',
            'action',
        ],
        'hidden' => [
            'ek_shop_has_store.shop_id',
            'ek_shop_has_store.store_id',
        ],
        'ric' => [
            'ek_shop_has_store.shop_id',
            'ek_shop_has_store.store_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_shop_has_store',
            ],
        ],
    ],
]);
