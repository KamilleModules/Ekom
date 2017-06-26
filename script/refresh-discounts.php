<?php


//require_once __DIR__ . "/../boot.php";
//require_once __DIR__ . "/../init.php";


use Core\Services\A;
use Module\Ekom\Api\EkomApi;

A::quickPdoInit();
//QuickPdo::changeErrorMode(\PDO::ERRMODE_WARNING);


$api = EkomApi::inst();
$api->discountLayer()->refreshDiscounts(null, [1280, 1500]);


