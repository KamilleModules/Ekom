<?php




$prc = "Ekom.kamille.ek_video";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_video.id',
            'ek_video.uri',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_video.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_video',
            ],
        ],
    ],
]);
