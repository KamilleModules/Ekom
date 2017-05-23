<?php



use Core\Services\A;
use Module\Ekom\Api\EkomApi;


//require_once __DIR__ . "/../boot.php";
//require_once __DIR__ . "/../init.php";


A::quickPdoInit();
//QuickPdo::changeErrorMode(\PDO::ERRMODE_WARNING);


$api = EkomApi::inst();
//--------------------------------------------
// CLEANING THE WHOLE DATABASE!!!
//--------------------------------------------
// deleting tables with more relationships at the beginning
$api->shop()->deleteAll();
$api->shopHasLang()->deleteAll();
$api->shopHasCurrency()->deleteAll();
$api->shopHasStore()->deleteAll();
$api->backofficeUser()->deleteAll();
$api->productAttributeLang()->deleteAll();
$api->productAttributeValueLang()->deleteAll();
$api->productHasProductAttribute()->deleteAll();
$api->shopHasProduct()->deleteAll();
$api->shopHasProductLang()->deleteAll();
$api->shopHasProductCard()->deleteAll();
$api->categoryLang()->deleteAll();

// no deps
$api->timezone()->deleteAll();
$api->currency()->deleteAll();
$api->lang()->deleteAll();
$api->store()->deleteAll();
$api->productAttribute()->deleteAll();
$api->productAttributeValue()->deleteAll();
$api->product()->deleteAll();
$api->productCard()->deleteAll();
$api->category()->deleteAll();


//--------------------------------------------
// FEEDING THE DATABASE WITH TEST DATA
//--------------------------------------------

//--------------------------------------------
// lang
//--------------------------------------------
$langFrench = $api->lang()->create([
    "label" => 'french',
    "iso_code" => 'fra',
]);

$langEnglish = $api->lang()->create([
    "label" => 'english',
    "iso_code" => 'eng',
]);


//--------------------------------------------
// backoffice user
//--------------------------------------------
$api->backofficeUser()->create([
    "email" => 'ling@me.com',
    "pass" => 'pass',
    "lang_id" => $langFrench,
]);

$api->backofficeUser()->create([
    "email" => 'anne@me.com',
    "pass" => 'pass',
    "lang_id" => $langEnglish,
]);


//--------------------------------------------
// timezone bullsheet
//--------------------------------------------
$timezones = DateTimeZone::listIdentifiers();
foreach ($timezones as $timezone) {
    $api->timezone()->create([
        "name" => $timezone,
    ]);
}


//--------------------------------------------
// currency
//--------------------------------------------
$currencyEuro = $api->currency()->create([
    "iso_code" => 'EUR',
    "symbol" => "€",
]);
$currencyDollar = $api->currency()->create([
    "iso_code" => 'USD',
    "symbol" => "$",
]);


//--------------------------------------------
// shop
//--------------------------------------------
$shopEurope = $api->shop()->create([
    "label" => 'Lee Europe',
    "host" => "lee",
    "lang_id" => $langFrench,
    "currency_id" => $currencyEuro,
    "timezone_id" => $api->timezone()->readColumn('id', [['name', '=', 'Europe/Paris']]),
]);
$shopUsa = $api->shop()->create([
    "label" => 'Lee U.S.A',
    "host" => "lee.us",
    "lang_id" => $langEnglish,
    "currency_id" => $currencyDollar,
    "timezone_id" => $api->timezone()->readColumn('id', [['name', '=', 'America/Los_Angeles']]),
]);


//--------------------------------------------
// shop has lang
//--------------------------------------------
$api->shopHasLang()->create([
    "shop_id" => $shopEurope,
    "lang_id" => $langFrench,
]);
$api->shopHasLang()->create([
    "shop_id" => $shopEurope,
    "lang_id" => $langEnglish,
]);
$api->shopHasLang()->create([
    "shop_id" => $shopUsa,
    "lang_id" => $langEnglish,
]);


//--------------------------------------------
// shop has currency
//--------------------------------------------
$api->shopHasCurrency()->create([
    "shop_id" => $shopEurope,
    "currency_id" => $currencyEuro,
]);
$api->shopHasCurrency()->create([
    "shop_id" => $shopEurope,
    "currency_id" => $currencyDollar,
]);

$api->shopHasCurrency()->create([
    "shop_id" => $shopUsa,
    "currency_id" => $currencyDollar,
]);


//--------------------------------------------
// store
//--------------------------------------------
$storeAvertin = $api->store()->create([
    "label" => "Store Saint-Avertin",
]);
$storeMiami = $api->store()->create([
    "label" => "Store Miami",
]);


//--------------------------------------------
// store
//--------------------------------------------
$api->shopHasStore()->create([
    "shop_id" => $shopEurope,
    "store_id" => $storeAvertin,
]);

$api->shopHasStore()->create([
    "shop_id" => $shopUsa,
    "store_id" => $storeMiami,
]);


//--------------------------------------------
// product attribute
//--------------------------------------------
$attrColor = $api->productAttribute()->create([
    "name" => 'color',
]);
$attrSize = $api->productAttribute()->create([
    "name" => 'size',
]);
$attrDiameter = $api->productAttribute()->create([
    "name" => 'diameter',
]);
$attrWeight = $api->productAttribute()->create([
    "name" => 'weight',
]);

//--------------------------------------------
// product attribute lang
//--------------------------------------------
$api->productAttributeLang()->create([
    "name" => 'couleur',
    "product_attribute_id" => $attrColor,
    "lang_id" => $langFrench,
]);
$api->productAttributeLang()->create([
    "name" => 'color',
    "product_attribute_id" => $attrColor,
    "lang_id" => $langEnglish,
]);
$api->productAttributeLang()->create([
    "name" => 'taille',
    "product_attribute_id" => $attrSize,
    "lang_id" => $langFrench,
]);
$api->productAttributeLang()->create([
    "name" => 'size',
    "product_attribute_id" => $attrSize,
    "lang_id" => $langEnglish,
]);

$api->productAttributeLang()->create([
    "name" => 'diamètre',
    "product_attribute_id" => $attrDiameter,
    "lang_id" => $langFrench,
]);
$api->productAttributeLang()->create([
    "name" => 'diameter',
    "product_attribute_id" => $attrDiameter,
    "lang_id" => $langEnglish,
]);

$api->productAttributeLang()->create([
    "name" => 'poids',
    "product_attribute_id" => $attrWeight,
    "lang_id" => $langFrench,
]);
$api->productAttributeLang()->create([
    "name" => 'weight',
    "product_attribute_id" => $attrWeight,
    "lang_id" => $langEnglish,
]);


//--------------------------------------------
// product attribute value and product attribute value lang
//--------------------------------------------
$colors = [
    'blue' => [
        'fra' => 'bleu',
    ],
    'red' => [
        'fra' => 'rouge',
    ],
    'green' => [
        'fra' => 'vert',
    ],
    'cyan' => [
        'fra' => 'cyan',
    ],
    'yellow' => [
        'fra' => 'jaune',
    ],
    'purple' => [
        'fra' => 'violet',
    ],
    'pink' => [
        'fra' => 'rose',
    ],
    'white' => [
        'fra' => 'blanc',
    ],
    'black' => [
        'fra' => 'noir',
    ],
];
foreach ($colors as $color => $translations) {
    $valueColor = $api->productAttributeValue()->create([
        "value" => $color,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $color,
        "product_attribute_value_id" => $valueColor,
        "lang_id" => $langEnglish,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $translations['fra'],
        "product_attribute_value_id" => $valueColor,
        "lang_id" => $langFrench,
    ]);

}


$sizes = [
    'S' => [
        'fra' => 'S',
    ],
    'M' => [
        'fra' => 'M',
    ],
    'L' => [
        'fra' => 'L',
    ],
];
foreach ($sizes as $size => $translations) {
    $valueSize = $api->productAttributeValue()->create([
        "value" => $size,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $size,
        "product_attribute_value_id" => $valueSize,
        "lang_id" => $langEnglish,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $translations['fra'],
        "product_attribute_value_id" => $valueSize,
        "lang_id" => $langFrench,
    ]);
}


$diameters = [
    '55 cm' => [
        'fra' => '55 cm',
    ],
    '65 cm' => [
        'fra' => '65 cm',
    ],
    '75 cm' => [
        'fra' => '75 cm',
    ],
];
foreach ($diameters as $diameter => $translations) {
    $valueDiameter = $api->productAttributeValue()->create([
        "value" => $diameter,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $diameter,
        "product_attribute_value_id" => $valueDiameter,
        "lang_id" => $langEnglish,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $translations['fra'],
        "product_attribute_value_id" => $valueDiameter,
        "lang_id" => $langFrench,
    ]);
}

$weights = [
    '4 kg' => [
        'fra' => '4 kg',
    ],
    '6 kg' => [
        'fra' => '6 kg',
    ],
    '8 kg' => [
        'fra' => '8 kg',
    ],
];
foreach ($weights as $weight => $translations) {
    $valueWeight = $api->productAttributeValue()->create([
        "value" => $weight,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $weight,
        "product_attribute_value_id" => $valueWeight,
        "lang_id" => $langEnglish,
    ]);

    $api->productAttributeValueLang()->create([
        "value" => $translations['fra'],
        "product_attribute_value_id" => $valueWeight,
        "lang_id" => $langFrench,
    ]);
}

//--------------------------------------------
// product card
//--------------------------------------------
$cardKettleBell = $api->productCard()->create([]);
$cardBallonPaille = $api->productCard()->create([]);
$cardPilatesRing = $api->productCard()->create([]);
$cardSocks = $api->productCard()->create([]);
$cardGravity = $api->productCard()->create([]);
$cardCagePower = $api->productCard()->create([]);
$cardTapis = $api->productCard()->create([]);
$cardBalanceBoard = $api->productCard()->create([]);
$cardBlocPrioception = $api->productCard()->create([]);
$cardBallon = $api->productCard()->create([]);


$cards = [
    $cardKettleBell,
    $cardBallonPaille,
    $cardPilatesRing,
    $cardSocks,
    $cardGravity,
    $cardCagePower,
    $cardTapis,
    $cardBalanceBoard,
    $cardBlocPrioception,
    $cardBallon,
];


//--------------------------------------------
// product card lang
//--------------------------------------------
$lorem = " Fusce pretium varius iaculis. Proin finibus nisl ut malesuada maximus. Nam vel tempus orci. Ut sed nunc nec odio dapibus mollis vitae id est. Mauris malesuada lacus ex, nec porttitor justo mattis eget. Quisque id dui sed metus auctor pellentesque. Ut quam nulla, porta in molestie ut, malesuada vel augue. Mauris tempor ante in ipsum ornare ullamcorper. Pellentesque vel tellus est. Praesent eget odio ante. Curabitur erat odio, tempor vel pulvinar et, tincidunt id lectus. Suspendisse vel quam semper, auctor arcu in, efficitur nulla. In hac habitasse platea dictumst. Fusce mollis fringilla diam non sollicitudin. Praesent arcu enim, semper ut faucibus bibendum, posuere in neque. Interdum et malesuada fames ac ante ipsum primis in faucibus. ";
$api->productCardLang()->create([
    "label" => "Kettle Bell",
    "description" => "$lorem",
    "slug" => "kettle-bell",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Ballon Paille",
    "description" => "$lorem",
    "slug" => "ballon-paille",
    "product_card_id" => $cardBallonPaille,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Pilates Ring",
    "description" => "$lorem",
    "slug" => "pilates-ring",
    "product_card_id" => $cardPilatesRing,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Chaussettes",
    "description" => "$lorem",
    "slug" => "socks",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Gravity",
    "description" => "$lorem",
    "slug" => "gravity",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Cage Power",
    "description" => "$lorem",
    "slug" => "cage-power",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Tapis",
    "description" => "$lorem",
    "slug" => "tapis",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Balance Board",
    "description" => "$lorem",
    "slug" => "balance-board",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Bloc Prioception",
    "description" => "$lorem",
    "slug" => "bloc-prioception",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Ballon",
    "description" => "$lorem",
    "slug" => "ballon",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langFrench,
]);

// english
$api->productCardLang()->create([
    "label" => "Kettle Bell",
    "description" => "$lorem",
    "slug" => "kettle-bell",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Straw Balloon",
    "description" => "$lorem",
    "slug" => "ballon-paille",
    "product_card_id" => $cardBallonPaille,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Pilates Ring",
    "description" => "$lorem",
    "slug" => "pilates-ring",
    "product_card_id" => $cardPilatesRing,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Socks",
    "description" => "$lorem",
    "slug" => "socks",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Gravity",
    "description" => "$lorem",
    "slug" => "gravity",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Cage Power",
    "description" => "$lorem",
    "slug" => "cage-power",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Tapis",
    "description" => "$lorem",
    "slug" => "tapis",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Balance Board",
    "description" => "$lorem",
    "slug" => "balance-board",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Prioception Bloc",
    "description" => "$lorem",
    "slug" => "bloc-prioception",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Balloon",
    "description" => "$lorem",
    "slug" => "ballon",
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
]);


//--------------------------------------------
// product
//--------------------------------------------
$productKettleBell_4 = $api->product()->create([
    "reference" => "REF_KETTLE_BELL_4KG",
    "weight" => "4",
    "price" => "12.60",
    "product_card_id" => $cardKettleBell,
]);
$productKettleBell_6 = $api->product()->create([
    "reference" => "REF_KETTLE_BELL_6KG",
    "weight" => "6",
    "price" => "18.90",
    "product_card_id" => $cardKettleBell,
]);
$productKettleBell_8 = $api->product()->create([
    "reference" => "REF_KETTLE_BELL_8KG",
    "weight" => "8",
    "price" => "25.20",
    "product_card_id" => $cardKettleBell,
]);

$productBallonPaille = $api->product()->create([
    "reference" => "REF_BALLON_PAILLE",
    "weight" => "0.80",
    "price" => "3.70",
    "product_card_id" => $cardBallonPaille,
]);

$productPilatesRingBlack = $api->product()->create([
    "reference" => "REF_PILATES_RING_NOIR",
    "weight" => "0.75",
    "price" => "14.73",
    "product_card_id" => $cardPilatesRing,
]);

$productPilatesRingBlue = $api->product()->create([
    "reference" => "REF_PILATES_RING_BLEU",
    "weight" => "0.75",
    "price" => "14.73",
    "product_card_id" => $cardPilatesRing,
]);

$productSocksBlackSmall = $api->product()->create([
    "reference" => "REF_CHAUSSETTES_NOIR_S",
    "weight" => "0.25",
    "price" => "5",
    "product_card_id" => $cardSocks,
]);

$productSocksBlackMedium = $api->product()->create([
    "reference" => "REF_CHAUSSETTES_NOIR_M",
    "weight" => "0.26",
    "price" => "5",
    "product_card_id" => $cardSocks,
]);


$productGravity = $api->product()->create([
    "reference" => "REF_GRAVITY",
    "weight" => "250",
    "price" => "3900",
    "product_card_id" => $cardGravity,
]);


$productCagePower = $api->product()->create([
    "reference" => "REF_CAGE_POWER",
    "weight" => "40",
    "price" => "3500",
    "product_card_id" => $cardCagePower,
]);


$productTapisBleu = $api->product()->create([
    "reference" => "REF_TAPIS_BLEU",
    "weight" => "1",
    "price" => "15",
    "product_card_id" => $cardTapis,
]);

$productBalanceBoard = $api->product()->create([
    "reference" => "REF_BALANCE_BOARD",
    "weight" => "1.35",
    "price" => "16.78",
    "product_card_id" => $cardBalanceBoard,
]);

$productBlocProprioception = $api->product()->create([
    "reference" => "REF_BLOC_PROPRIOCEPTION",
    "weight" => "0.85",
    "price" => "32.66",
    "product_card_id" => $cardBlocPrioception,
]);

$productBallonGris55cm = $api->product()->create([
    "reference" => "REF_BALLON_55CM",
    "weight" => "1",
    "price" => "10.80",
    "product_card_id" => $cardBallon,
]);

$productBallonGris65cm = $api->product()->create([
    "reference" => "REF_BALLON_65CM",
    "weight" => "1",
    "price" => "11.80",
    "product_card_id" => $cardBallon,
]);

$productBallonGris75cm = $api->product()->create([
    "reference" => "REF_BALLON_75CM",
    "weight" => "1",
    "price" => "12.80",
    "product_card_id" => $cardBallon,
]);

$products = [
    $productTapisBleu,
    $productSocksBlackSmall,
    $productSocksBlackMedium,
    $productGravity,
    $productCagePower,
    $productBlocProprioception,
    $productBalanceBoard,
    $productKettleBell_4,
    $productKettleBell_6,
    $productKettleBell_8,
    $productBallonGris55cm,
    $productBallonGris65cm,
    $productBallonGris75cm,
    $productBallonPaille,
    $productPilatesRingBlue,
    $productPilatesRingBlack,
];


//--------------------------------------------
// product lang
//--------------------------------------------
$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productKettleBell_4,
    "lang_id" => $langFrench,
]);
$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productKettleBell_6,
    "lang_id" => $langFrench,
]);
$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productKettleBell_8,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productBallonPaille,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productPilatesRingBlack,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productPilatesRingBlue,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productSocksBlackSmall,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productSocksBlackMedium,
    "lang_id" => $langFrench,
]);


$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productGravity,
    "lang_id" => $langFrench,
]);


$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productCagePower,
    "lang_id" => $langFrench,
]);


$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productTapisBleu,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productBalanceBoard,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productBlocProprioception,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "Ballon Gris 55cm",
    "description" => "",
    "product_id" => $productBallonGris55cm,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "Ballon Gris 65cm",
    "description" => "",
    "product_id" => $productBallonGris65cm,
    "lang_id" => $langFrench,
]);

$api->productLang()->create([
    "label" => "Ballon Gris 75cm",
    "description" => "",
    "product_id" => $productBallonGris75cm,
    "lang_id" => $langFrench,
]);


// english
$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productKettleBell_4,
    "lang_id" => $langEnglish,
]);
$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productKettleBell_6,
    "lang_id" => $langEnglish,
]);
$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productKettleBell_8,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productBallonPaille,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productPilatesRingBlack,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productPilatesRingBlue,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productSocksBlackSmall,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productSocksBlackMedium,
    "lang_id" => $langEnglish,
]);


$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productGravity,
    "lang_id" => $langEnglish,
]);


$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productCagePower,
    "lang_id" => $langEnglish,
]);


$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productTapisBleu,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productBalanceBoard,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "",
    "description" => "",
    "product_id" => $productBlocProprioception,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "Gray Balloon 55cm",
    "description" => "",
    "product_id" => $productBallonGris55cm,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "Gray Balloon 65cm",
    "description" => "",
    "product_id" => $productBallonGris65cm,
    "lang_id" => $langEnglish,
]);

$api->productLang()->create([
    "label" => "Gray Balloon 75cm",
    "description" => "",
    "product_id" => $productBallonGris75cm,
    "lang_id" => $langEnglish,
]);


//--------------------------------------------
// product has product attribute
//--------------------------------------------
$api->productHasProductAttribute()->create([
    "product_id" => $productPilatesRingBlue,
    "product_attribute_id" => $attrColor,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', 'blue']]),
]);
$api->productHasProductAttribute()->create([
    "product_id" => $productPilatesRingBlack,
    "product_attribute_id" => $attrColor,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', 'black']]),
]);

$api->productHasProductAttribute()->create([
    "product_id" => $productBallonGris55cm,
    "product_attribute_id" => $attrColor,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', '55 cm']]),
]);

$api->productHasProductAttribute()->create([
    "product_id" => $productBallonGris65cm,
    "product_attribute_id" => $attrColor,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', '65 cm']]),
]);

$api->productHasProductAttribute()->create([
    "product_id" => $productBallonGris75cm,
    "product_attribute_id" => $attrColor,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', '75 cm']]),
]);

$api->productHasProductAttribute()->create([
    "product_id" => $productKettleBell_4,
    "product_attribute_id" => $attrWeight,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', '4 kg']]),
]);
$api->productHasProductAttribute()->create([
    "product_id" => $productKettleBell_6,
    "product_attribute_id" => $attrWeight,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', '6 kg']]),
]);
$api->productHasProductAttribute()->create([
    "product_id" => $productKettleBell_8,
    "product_attribute_id" => $attrWeight,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', '8 kg']]),
]);


$api->productHasProductAttribute()->create([
    "product_id" => $productSocksBlackSmall,
    "product_attribute_id" => $attrSize,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', 'S']]),
]);
$api->productHasProductAttribute()->create([
    "product_id" => $productSocksBlackMedium,
    "product_attribute_id" => $attrSize,
    "product_attribute_value_id" => $api->productAttributeValue()->readColumn('id', [['value', '=', 'M']]),
]);


//--------------------------------------------
// store has product
//--------------------------------------------
$accidentallyMissingProducts = [
    $productPilatesRingBlack,
];
foreach ($products as $product) {

    if (in_array($product, $accidentallyMissingProducts, true)) {
        continue; // testing the unknown keyword synopsis
    }
    $api->storeHasProduct()->create([
        "store_id" => $storeAvertin,
        "product_id" => $product,
        "quantity" => rand(50, 100),
    ]);

    $api->storeHasProduct()->create([
        "store_id" => $storeMiami,
        "product_id" => $product,
        "quantity" => rand(50, 100),
    ]);
}





//--------------------------------------------
// category
//--------------------------------------------
$categoryEquipement = $api->category()->create([
    "name" => "equipement",
    "category_id" => null,
]);
$categoryFormation = $api->category()->create([
    "name" => "formation",
    "category_id" => null,
]);
$categoryMateriel = $api->category()->create([
    "name" => "materiel",
    "category_id" => $categoryEquipement,
]);
$categoryDiscipline = $api->category()->create([
    "name" => "discipline",
    "category_id" => $categoryMateriel,
]);
$categoryUtilisation = $api->category()->create([
    "name" => "utilisation",
    "category_id" => $categoryMateriel,
]);
$categoryEquilibre = $api->category()->create([
    "name" => "equilibre",
    "category_id" => $categoryUtilisation,
]);
$categoryPilates = $api->category()->create([
    "name" => "pilates",
    "category_id" => $categoryDiscipline,
]);

$categoryCrossTraining = $api->category()->create([
    "name" => "crossTraining",
    "category_id" => $categoryDiscipline,
]);

$categories = [
    $categoryEquipement,
    $categoryFormation,
    $categoryMateriel,
    $categoryDiscipline,
    $categoryUtilisation,
    $categoryEquilibre,
    $categoryPilates,
    $categoryCrossTraining,
];

//--------------------------------------------
// category lang
//--------------------------------------------
$lorem = "Pellentesque ultricies neque mauris, ac faucibus erat lobortis eu. Aliquam erat volutpat. Donec magna orci, bibendum non arcu porttitor, ornare consectetur mi. Vivamus at egestas urna. Quisque ut metus nisl. Duis sed neque tellus. Sed porta, nisi ac posuere consectetur, ante enim rutrum nunc, quis rutrum lorem leo commodo tellus. Donec in pulvinar urna. ";
$api->categoryLang()->create([
    "label" => "équipement",
    "description" => $lorem,
    "category_id" => $categoryEquipement,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "formation",
    "description" => $lorem,
    "category_id" => $categoryFormation,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "discipline",
    "description" => $lorem,
    "category_id" => $categoryDiscipline,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "pilates",
    "description" => $lorem,
    "category_id" => $categoryPilates,
    "lang_id" => $langFrench,
]);

$api->categoryLang()->create([
    "label" => "cross-training",
    "description" => $lorem,
    "category_id" => $categoryCrossTraining,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "matériel",
    "description" => $lorem,
    "category_id" => $categoryMateriel,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "utilisation",
    "description" => $lorem,
    "category_id" => $categoryUtilisation,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "équilibre",
    "description" => $lorem,
    "category_id" => $categoryEquilibre,
    "lang_id" => $langFrench,
]);


// english
$api->categoryLang()->create([
    "label" => "equipment",
    "description" => $lorem,
    "category_id" => $categoryEquipement,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "course",
    "description" => $lorem,
    "category_id" => $categoryFormation,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "discipline",
    "description" => $lorem,
    "category_id" => $categoryDiscipline,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "pilates",
    "description" => $lorem,
    "category_id" => $categoryPilates,
    "lang_id" => $langEnglish,
]);

$api->categoryLang()->create([
    "label" => "cross-training",
    "description" => $lorem,
    "category_id" => $categoryCrossTraining,
    "lang_id" => $langEnglish,
]);

$api->categoryLang()->create([
    "label" => "supplies",
    "description" => $lorem,
    "category_id" => $categoryMateriel,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "usage",
    "description" => $lorem,
    "category_id" => $categoryUtilisation,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "balance",
    "description" => $lorem,
    "category_id" => $categoryEquilibre,
    "lang_id" => $langEnglish,
]);




//--------------------------------------------
// shop has product card
//--------------------------------------------
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardTapis,
    "product_id" => $productTapisBleu,
    "category_id" => $categoryPilates,
]);

$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardSocks,
    "product_id" => $productSocksBlackMedium,
    "category_id" => $categoryPilates,
]);

$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardGravity,
    "product_id" => $productGravity,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardCagePower,
    "product_id" => $productCagePower,
    "category_id" => $categoryCrossTraining,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBlocPrioception,
    "product_id" => $productBlocProprioception,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBalanceBoard,
    "product_id" => $productBalanceBoard,
    "category_id" => $categoryEquilibre,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardKettleBell,
    "product_id" => $productKettleBell_6,
    "category_id" => $categoryCrossTraining,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBallon,
    "product_id" => $productBallonGris55cm,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBallonPaille,
    "product_id" => null,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardPilatesRing,
    "product_id" => $productPilatesRingBlue,
    "category_id" => $categoryPilates,
]);


$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardTapis,
    "product_id" => $productTapisBleu,
    "category_id" => $categoryPilates,
]);

$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardSocks,
    "product_id" => $productSocksBlackMedium,
    "category_id" => $categoryPilates,
]);

$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardGravity,
    "product_id" => $productGravity,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardCagePower,
    "product_id" => $productCagePower,
    "category_id" => $categoryCrossTraining,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardBlocPrioception,
    "product_id" => $productBlocProprioception,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardBalanceBoard,
    "product_id" => null,
    "category_id" => $categoryEquilibre,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardKettleBell,
    "product_id" => $productKettleBell_4,
    "category_id" => $categoryCrossTraining,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardBallon,
    "product_id" => $productBallonGris75cm,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardBallonPaille,
    "product_id" => $productBallonPaille,
    "category_id" => $categoryPilates,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardPilatesRing,
    "product_id" => $productPilatesRingBlue,
    "category_id" => $categoryPilates,
]);


//--------------------------------------------
// shop has product
//--------------------------------------------
$inactive = [
    $productPilatesRingBlack,
];
$pricesEurope = [
    $productBallonPaille => 5,
];
foreach ($products as $product) {

    $active = (in_array($product, $inactive, true)) ? 0 : 1;
    $price = (array_key_exists($product, $pricesEurope)) ? $pricesEurope[$product] : null;

    $api->shopHasProduct()->create([
        "shop_id" => $shopEurope,
        "product_id" => $product,
        "price" => $price,
        "active" => $active,
    ]);
    $api->shopHasProduct()->create([
        "shop_id" => $shopUsa,
        "product_id" => $product,
        "price" => null,
        "active" => $active,
    ]);
}


//--------------------------------------------
// shop has product lang
//--------------------------------------------
$slugs = [
    $productBallonPaille => "hum-ballon-paille",
];
$labels = [
    $productBallonGris55cm => "Le super ballon gris 55 cm",
];
foreach ($products as $product) {

    $slug = (array_key_exists($product, $slugs)) ? $slugs[$product] : "";
    $label = (array_key_exists($product, $labels)) ? $labels[$product] : "";

    $api->shopHasProductLang()->create([
        "label" => $label,
        "description" => "",
        "slug" => $slug,
        "shop_has_product_shop_id" => $shopEurope,
        "shop_has_product_product_id" => $product,
        "lang_id" => $langFrench,
    ]);
    $api->shopHasProductLang()->create([
        "label" => "",
        "description" => "",
        "slug" => "",
        "shop_has_product_shop_id" => $shopEurope,
        "shop_has_product_product_id" => $product,
        "lang_id" => $langEnglish,
    ]);
    $api->shopHasProductLang()->create([
        "label" => "",
        "description" => "",
        "slug" => "",
        "shop_has_product_shop_id" => $shopUsa,
        "shop_has_product_product_id" => $product,
        "lang_id" => $langEnglish,
    ]);
}





