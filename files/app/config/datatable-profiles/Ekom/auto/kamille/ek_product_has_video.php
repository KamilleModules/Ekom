<?php




$prc = "Ekom.kamille.ek_product_has_video";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_has_video.product_id',
            'ek_product_has_video.video_id',
            'ek_product.product_reference_id',
            'ek_video.uri',
            'action',
        ],
        'hidden' => [
            'ek_product_has_video.product_id',
            'ek_product_has_video.video_id',
        ],
        'ric' => [
            'ek_product_has_video.product_id',
            'ek_product_has_video.video_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_has_video',
            ],
        ],
    ],
]);
