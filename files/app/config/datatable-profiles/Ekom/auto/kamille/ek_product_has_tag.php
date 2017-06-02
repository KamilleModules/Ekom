<?php




$prc = "Ekom.kamille.ek_product_has_tag";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_has_tag.product_id',
            'ek_product_has_tag.tag_id',
            'ek_product.product_reference_id',
            'ek_tag.label',
            'ek_shop.label',
            'action',
        ],
        'hidden' => [
            'ek_product_has_tag.product_id',
            'ek_product_has_tag.tag_id',
            'ek_product_has_tag.shop_id',
        ],
        'ric' => [
            'ek_product_has_tag.product_id',
            'ek_product_has_tag.tag_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_has_tag',
            ],
        ],
    ],
]);
