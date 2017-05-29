<?php


use Module\Ekom\Api\EkomApi;

require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";



$taxes = [
    [
        "label" => "t20",
        "amount" => "20.000000",
        "tax_id" => "1",
        "order" => "0",
        "mode" => "",
        "group_label" => "france tva",
        "condition" => "",
    ],
    [
        "label" => "t4.5",
        "amount" => "4.500000",
        "tax_id" => "2",
        "order" => "3",
        "mode" => "merge",
        "group_label" => "france tva",
        "condition" => "",
    ],
    [
        "label" => "t5.5",
        "amount" => "5.500000",
        "tax_id" => "3",
        "order" => "2",
        "mode" => "merge",
        "group_label" => "france tva",
        "condition" => "",
    ],
    [
        "label" => "t10",
        "amount" => "10.000000",
        "tax_id" => "4",
        "order" => "1",
        "mode" => "",
        "group_label" => "france tva",
        "condition" => "",
    ],
];


$price = 100;
a($taxes);
$details=[];
a(EkomApi::inst()->taxLayer()->applyTaxesToPrice($taxes, $price, $details));
a($details);
