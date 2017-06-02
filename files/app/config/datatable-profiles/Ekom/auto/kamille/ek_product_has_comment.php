<?php




$prc = "Ekom.kamille.ek_product_has_comment";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_has_comment.product_id',
            'ek_product_has_comment.comment_id',
            'ek_product.product_reference_id',
            'ek_comment.active',
            'action',
        ],
        'hidden' => [
            'ek_product_has_comment.product_id',
            'ek_product_has_comment.comment_id',
        ],
        'ric' => [
            'ek_product_has_comment.product_id',
            'ek_product_has_comment.comment_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_has_comment',
            ],
        ],
    ],
]);
