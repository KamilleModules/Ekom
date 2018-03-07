<?php


$operations = [
    [
        "operationType" => "create",
        "elementType" => "simple", // simple|context,    remove this to use the bot's default algo
        "icon" => "fa fa-bomb",
        "elementTable" => "ek_product_group",
        "elementName" => "product_group",
        "elementLabel" => "product group",
        "elementLabelPlural" => "product groups",
        "elementRoute" => "NullosAdmin_Ekom_ProductGroup_List",
        "ric" => [
            "id",
        ],
    ],
    [
        "operationType" => "create",
        "elementType" => "context",
        "icon" => "fa fa-bomb",
        "elementTable" => "ek_seller_has_address",
        "elementName" => "seller_has_address",
        "elementLabel" => "seller address",
        "elementLabelPlural" => "seller addresses",
        "elementRoute" => "NullosAdmin_Ekom_SellerAddress_List",
        "ric" => [
            "seller_id",
            "address_id",
        ],
    ],
];


$configuration = [
    "dbPrefixes" => [
        'ek_',
    ],
    "columnLabels" => [
        "tables" => [],
        "default" => [],
    ],
];