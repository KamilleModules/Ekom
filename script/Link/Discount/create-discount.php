<?php


use Core\Services\A;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Link\Discount\DiscountLink;
use Module\Ekom\Api\Object\Discount;
use QuickPdo\QuickPdo;
use SaveOrm\ObjectManager\ObjectManager;


require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";


A::testInit();



$o = new DiscountLink();


$arr = [
    // productCardId => percent
    2 => 10,
    284 => 20,
    334 => 20,
    389 => 30,
];


foreach ($arr as $productCard => $percent) {

    $ret = $o->saveDiscountForProductCard($productCard, [
        'shop_id' => 1,
        'lang_id' => 1,
        'label' => '-' . $percent . '% sur card #' . $productCard,
        'procedure_type' => 'percent',
        'procedure_operand' => $percent,
        'user_group' => null,
        'currency' => null,
        'date_start' => null,
        'date_end' => null,
    ]);
    a($ret);

}