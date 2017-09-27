<?php


use Core\Services\A;
use Module\Ekom\Api\EkomApi;



require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";


A::quickPdoInit();


$statuses = [
    [
        'code' => 'payment_sent',
        'color' => '#FFFF00',
    ],
    [
        'code' => 'payment_accepted',
        'color' => '#80FF00',
    ],
    [
        'code' => 'payment_verified',
        'color' => '#00FF00',
    ],
    [
        'code' => 'preparing_order',
        'color' => '#008080',
    ],
    [
        'code' => 'order_shipped',
        'color' => '#29ABE2',
    ],
    [
        'code' => 'order_delivered',
        'color' => '#0000FF',
    ],
    [
        'code' => 'payment_error',
        'color' => '#FF0000',
    ],
    [
        'code' => 'preparing_order_error',
        'color' => '#F15A24',
    ],
    [
        'code' => 'shipping_error',
        'color' => '#F7931E',
    ],
    [
        'code' => 'order_delivered_error',
        'color' => '#FBB03B',
    ],
    [
        'code' => 'canceled',
        'color' => '#9E005D',
    ],
    [
        'code' => 'reimbursed',
        'color' => '#662D91',
    ],
];
EkomApi::inst()->statusLayer()->createStatuses($statuses, false, 1, 1);


