<?php


use Core\Services\A;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;


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
$api->backofficeUser()->deleteAll();
$api->productAttributeLang()->deleteAll();
$api->productAttributeValueLang()->deleteAll();
$api->productHasProductAttribute()->deleteAll();
$api->shopHasProduct()->deleteAll();
$api->shopHasProductCard()->deleteAll();
$api->shopHasProductCardLang()->deleteAll();
$api->shopHasProductLang()->deleteAll();
$api->categoryLang()->deleteAll();
$api->categoryHasProductCard()->deleteAll();
$api->taxLang()->deleteAll();
$api->productCardHasTaxGroup()->deleteAll();
$api->taxGroupHasTax()->deleteAll();
$api->taxGroup()->deleteAll();
$api->user()->deleteAll();
$api->productHasDiscount()->deleteAll();
$api->productCardHasDiscount()->deleteAll();
$api->categoryHasDiscount()->deleteAll();
$api->discountLang()->deleteAll();
$api->discount()->deleteAll();
$api->userHasUserGroup()->deleteAll();
$api->orderHasOrderStatus()->deleteAll();
$api->orderStatusLang()->deleteAll();
$api->orderStatus()->deleteAll();
$api->order()->deleteAll();
$api->couponHasCartDiscount()->deleteAll();
$api->cartDiscountLang()->deleteAll();
$api->cartDiscount()->deleteAll();
$api->coupon()->deleteAll();
$api->countryLang()->deleteAll();
$api->userHasAddress()->deleteAll();
$api->address()->deleteAll();
$api->shopHasCarrier()->deleteAll();
$api->shopHasPaymentMethod()->deleteAll();



// no deps
$api->timezone()->deleteAll();
$api->currency()->deleteAll();
$api->lang()->deleteAll();
$api->productAttribute()->deleteAll();
$api->productAttributeValue()->deleteAll();
$api->product()->deleteAll();
$api->productCard()->deleteAll();
$api->category()->deleteAll();
$api->tax()->deleteAll();
$api->userGroup()->deleteAll();
$api->country()->deleteAll();
$api->carrier()->deleteAll();
$api->paymentMethod()->deleteAll();


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
    "lang_iso" => "fra",
]);

$api->backofficeUser()->create([
    "email" => 'anne@me.com',
    "pass" => 'pass',
    "lang_iso" => "eng",
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

$shops = [
    $shopEurope,
    $shopUsa,
];

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
    "exchange_rate" => "1",
    "active" => "1",
]);
$api->shopHasCurrency()->create([
    "shop_id" => $shopEurope,
    "currency_id" => $currencyDollar,
    "exchange_rate" => "1.11998",
    "active" => "0",
]);

$api->shopHasCurrency()->create([
    "shop_id" => $shopUsa,
    "currency_id" => $currencyDollar,
    "exchange_rate" => "1",
    "active" => "1",
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
    "product_card_id" => $cardSocks,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Gravity",
    "description" => "$lorem",
    "slug" => "gravity",
    "product_card_id" => $cardGravity,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Cage Power",
    "description" => "$lorem",
    "slug" => "cage-power",
    "product_card_id" => $cardCagePower,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Tapis",
    "description" => "$lorem",
    "slug" => "tapis",
    "product_card_id" => $cardTapis,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Balance Board",
    "description" => "$lorem",
    "slug" => "balance-board",
    "product_card_id" => $cardBalanceBoard,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Bloc Prioception",
    "description" => "$lorem",
    "slug" => "bloc-prioception",
    "product_card_id" => $cardBlocPrioception,
    "lang_id" => $langFrench,
]);
$api->productCardLang()->create([
    "label" => "Ballon",
    "description" => "$lorem",
    "slug" => "ballon",
    "product_card_id" => $cardBallon,
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
    "product_card_id" => $cardSocks,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Gravity",
    "description" => "$lorem",
    "slug" => "gravity",
    "product_card_id" => $cardGravity,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Cage Power",
    "description" => "$lorem",
    "slug" => "cage-power",
    "product_card_id" => $cardCagePower,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Tapis",
    "description" => "$lorem",
    "slug" => "tapis",
    "product_card_id" => $cardTapis,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Balance Board",
    "description" => "$lorem",
    "slug" => "balance-board",
    "product_card_id" => $cardBalanceBoard,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Prioception Bloc",
    "description" => "$lorem",
    "slug" => "bloc-prioception",
    "product_card_id" => $cardBlocPrioception,
    "lang_id" => $langEnglish,
]);
$api->productCardLang()->create([
    "label" => "Balloon",
    "description" => "$lorem",
    "slug" => "ballon",
    "product_card_id" => $cardBallon,
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
// category
//--------------------------------------------
$categoryEquipement = $api->category()->create([
    "name" => "equipement",
    "category_id" => null,
    "shop_id" => $shopEurope,
]);
$categoryFormation = $api->category()->create([
    "name" => "formation",
    "category_id" => null,
    "shop_id" => $shopEurope,
]);
$categoryMateriel = $api->category()->create([
    "name" => "materiel",
    "category_id" => $categoryEquipement,
    "shop_id" => $shopEurope,
]);
$categoryDiscipline = $api->category()->create([
    "name" => "discipline",
    "category_id" => $categoryMateriel,
    "shop_id" => $shopEurope,
]);
$categoryUtilisation = $api->category()->create([
    "name" => "utilisation",
    "category_id" => $categoryMateriel,
    "shop_id" => $shopEurope,
]);
$categoryEquilibre = $api->category()->create([
    "name" => "equilibre",
    "category_id" => $categoryUtilisation,
    "shop_id" => $shopEurope,
]);
$categoryPilates = $api->category()->create([
    "name" => "pilates",
    "category_id" => $categoryDiscipline,
    "shop_id" => $shopEurope,
]);

$categoryCrossTraining = $api->category()->create([
    "name" => "crossTraining",
    "category_id" => $categoryDiscipline,
    "shop_id" => $shopEurope,
]);


$categoryUsFormation = $api->category()->create([
    "name" => "formation",
    "category_id" => null,
    "shop_id" => $shopUsa,
]);


//--------------------------------------------
// category has product card
//--------------------------------------------
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardTapis,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardSocks,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardPilatesRing,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardKettleBell,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardGravity,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardCagePower,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardBlocPrioception,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardBallonPaille,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardBalanceBoard,
]);
$api->categoryHasProductCard()->create([
    "category_id" => $categoryPilates,
    "product_card_id" => $cardBallon,
]);


$api->categoryHasProductCard()->create([
    "category_id" => $categoryUsFormation,
    "product_card_id" => $cardTapis,
]);


//--------------------------------------------
// category lang
//--------------------------------------------
$lorem = "Pellentesque ultricies neque mauris, ac faucibus erat lobortis eu. Aliquam erat volutpat. Donec magna orci, bibendum non arcu porttitor, ornare consectetur mi. Vivamus at egestas urna. Quisque ut metus nisl. Duis sed neque tellus. Sed porta, nisi ac posuere consectetur, ante enim rutrum nunc, quis rutrum lorem leo commodo tellus. Donec in pulvinar urna. ";
$api->categoryLang()->create([
    "label" => "équipement",
    "description" => $lorem,
    "slug" => "equipement",
    "category_id" => $categoryEquipement,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "formation",
    "description" => $lorem,
    "slug" => "formation",
    "category_id" => $categoryFormation,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "discipline",
    "description" => $lorem,
    "slug" => "discipline",
    "category_id" => $categoryDiscipline,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "pilates",
    "description" => $lorem,
    "slug" => "pilates",
    "category_id" => $categoryPilates,
    "lang_id" => $langFrench,
]);

$api->categoryLang()->create([
    "label" => "cross-training",
    "description" => $lorem,
    "slug" => "cross-training",
    "category_id" => $categoryCrossTraining,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "matériel",
    "description" => $lorem,
    "slug" => "materiel",
    "category_id" => $categoryMateriel,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "utilisation",
    "description" => $lorem,
    "slug" => "utilisation",
    "category_id" => $categoryUtilisation,
    "lang_id" => $langFrench,
]);
$api->categoryLang()->create([
    "label" => "équilibre",
    "description" => $lorem,
    "slug" => "equilibre",
    "category_id" => $categoryEquilibre,
    "lang_id" => $langFrench,
]);


// english
$api->categoryLang()->create([
    "label" => "equipment",
    "description" => $lorem,
    "slug" => "equipement",
    "category_id" => $categoryEquipement,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "course",
    "description" => $lorem,
    "slug" => "course",
    "category_id" => $categoryFormation,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "discipline",
    "description" => $lorem,
    "slug" => "discipline",
    "category_id" => $categoryDiscipline,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "pilates",
    "description" => $lorem,
    "slug" => "pilates",
    "category_id" => $categoryPilates,
    "lang_id" => $langEnglish,
]);

$api->categoryLang()->create([
    "label" => "cross-training",
    "description" => $lorem,
    "slug" => "cross-training",
    "category_id" => $categoryCrossTraining,
    "lang_id" => $langEnglish,
]);

$api->categoryLang()->create([
    "label" => "supplies",
    "description" => $lorem,
    "slug" => "supplies",
    "category_id" => $categoryMateriel,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "usage",
    "description" => $lorem,
    "slug" => "usage",
    "category_id" => $categoryUtilisation,
    "lang_id" => $langEnglish,
]);
$api->categoryLang()->create([
    "label" => "balance",
    "description" => $lorem,
    "slug" => "balance",
    "category_id" => $categoryEquilibre,
    "lang_id" => $langEnglish,
]);


$api->categoryLang()->create([
    "label" => "course",
    "description" => $lorem,
    "slug" => "course",
    "category_id" => $categoryUsFormation,
    "lang_id" => $langEnglish,
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
$products2Qty = [
    $productKettleBell_8 => 0,
    $productKettleBell_6 => 15,
    $productKettleBell_4 => 30,
];
foreach ($products as $product) {

    if (array_key_exists($product, $products2Qty)) {
        $quantity = $products2Qty[$product];
    } else {
        $quantity = rand(50, 100);
    }

    $active = (in_array($product, $inactive, true)) ? 0 : 1;
    if (array_key_exists($product, $pricesEurope)) {
        $price = $pricesEurope[$product];
        $wholesalePrice = $price - ($price / 2);
    } else {
        $price = null;
        $wholesalePrice = 2;
    }


    $api->shopHasProduct()->create([
        "shop_id" => $shopEurope,
        "product_id" => $product,
        "price" => $price,
        "wholesale_price" => $wholesalePrice,
        "quantity" => $quantity,
        "active" => $active,
    ]);
    $api->shopHasProduct()->create([
        "shop_id" => $shopUsa,
        "product_id" => $product,
        "price" => null,
        "wholesale_price" => $wholesalePrice,
        "quantity" => $quantity,
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
        "out_of_stock_text" => "Disponible sous 2 à 3 semaines",
        "shop_id" => $shopEurope,
        "product_id" => $product,
        "lang_id" => $langFrench,
    ]);
    $api->shopHasProductLang()->create([
        "label" => "",
        "description" => "",
        "slug" => "",
        "out_of_stock_text" => "Available within 2 weeks",
        "shop_id" => $shopEurope,
        "product_id" => $product,
        "lang_id" => $langEnglish,
    ]);
    $api->shopHasProductLang()->create([
        "label" => "",
        "description" => "",
        "slug" => "",
        "out_of_stock_text" => "Sold out!",
        "shop_id" => $shopUsa,
        "product_id" => $product,
        "lang_id" => $langEnglish,
    ]);
}


//--------------------------------------------
// shop has product card
//--------------------------------------------
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBallon,
    "product_id" => $productBallonGris55cm,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBalanceBoard,
    "product_id" => $productBalanceBoard,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBallonPaille,
    "product_id" => $productBallonPaille,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardBlocPrioception,
    "product_id" => $productBlocProprioception,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardCagePower,
    "product_id" => $productCagePower,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardGravity,
    "product_id" => $productGravity,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardKettleBell,
    "product_id" => $productKettleBell_6,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardPilatesRing,
    "product_id" => $productPilatesRingBlack,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardSocks,
    "product_id" => $productSocksBlackMedium,
    "active" => 0,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopEurope,
    "product_card_id" => $cardTapis,
    "product_id" => $productTapisBleu,
    "active" => 1,
]);

// usa
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardSocks,
    "product_id" => $productSocksBlackSmall,
    "active" => 1,
]);
$api->shopHasProductCard()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardKettleBell,
    "product_id" => $productKettleBell_4,
    "active" => 1,
]);


//--------------------------------------------
// shop has product card lang
//--------------------------------------------
$card2Slug = [
    $cardKettleBell => "the-kettle-bell",
    $cardBlocPrioception => "the-bloc-prioception",
];
foreach ($cards as $card) {

    $slug = (array_key_exists($card, $card2Slug)) ? $card2Slug[$card] : "";

    $api->shopHasProductCardLang()->create([
        "shop_id" => $shopEurope,
        "product_card_id" => $card,
        "lang_id" => $langFrench,
        "label" => "",
        "slug" => $slug,
        "description" => "",
    ]);

    $api->shopHasProductCardLang()->create([
        "shop_id" => $shopEurope,
        "product_card_id" => $card,
        "lang_id" => $langEnglish,
        "label" => "",
        "slug" => "",
        "description" => "",
    ]);
}


$api->shopHasProductCardLang()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardSocks,
    "lang_id" => $langEnglish,
    "label" => "",
    "slug" => "",
    "description" => "",
]);

$api->shopHasProductCardLang()->create([
    "shop_id" => $shopUsa,
    "product_card_id" => $cardKettleBell,
    "lang_id" => $langEnglish,
    "label" => "",
    "slug" => "",
    "description" => "",
]);


//--------------------------------------------
// tax
//--------------------------------------------
$tax20 = $api->tax()->create([
    'amount' => "20",
]);

$tax10 = $api->tax()->create([
    'amount' => "10",
]);

$tax50 = $api->tax()->create([
    'amount' => "50",
]);


//--------------------------------------------
// tax lang
//--------------------------------------------
$api->taxLang()->create([
    'tax_id' => $tax20,
    'lang_id' => $langFrench,
    'label' => "tva 20%",
]);

$api->taxLang()->create([
    'tax_id' => $tax20,
    'lang_id' => $langEnglish,
    'label' => "vat 20%",
]);


$api->taxLang()->create([
    'tax_id' => $tax10,
    'lang_id' => $langFrench,
    'label' => "taxe exemple 10%",
]);

$api->taxLang()->create([
    'tax_id' => $tax10,
    'lang_id' => $langEnglish,
    'label' => "example tax 10%",
]);

$api->taxLang()->create([
    'tax_id' => $tax50,
    'lang_id' => $langFrench,
    'label' => "taxe exemple 50%",
]);

$api->taxLang()->create([
    'tax_id' => $tax50,
    'lang_id' => $langEnglish,
    'label' => "example tax 50%",
]);


//--------------------------------------------
// tax group
//--------------------------------------------
$taxGroupFrance = $api->taxGroup()->create([
    'label' => "france tva",
    'condition' => "",
    'shop_id' => $shopEurope,
]);

$taxGroupDemo = $api->taxGroup()->create([
    'label' => "tax demo 50%",
    'condition' => "",
    'shop_id' => $shopEurope,
]);


//--------------------------------------------
// tax group has tax
//--------------------------------------------
$api->taxGroupHasTax()->create([
    "tax_group_id" => $taxGroupFrance,
    "tax_id" => $tax20,
    "order" => 0,
    "mode" => "",
]);

$api->taxGroupHasTax()->create([
    "tax_group_id" => $taxGroupFrance,
    "tax_id" => $tax10,
    "order" => 1,
    "mode" => "",
]);

$api->taxGroupHasTax()->create([
    "tax_group_id" => $taxGroupDemo,
    "tax_id" => $tax50,
    "order" => 0,
    "mode" => "",
]);


//--------------------------------------------
// product card has tax group
//--------------------------------------------
foreach ($cards as $card) {
    if ($cardBallonPaille === $card) {
        $api->productCardHasTaxGroup()->create([
            "shop_id" => $shopEurope,
            "product_card_id" => $card,
            "tax_group_id" => $taxGroupDemo,
        ]);
    } else {
        $api->productCardHasTaxGroup()->create([
            "shop_id" => $shopEurope,
            "product_card_id" => $card,
            "tax_group_id" => $taxGroupFrance,
        ]);
    }
}


//--------------------------------------------
// user group
//--------------------------------------------
$userGroupB2B = $api->userGroup()->create([
    "name" => "b2b",
    "shop_id" => $shopEurope,
]);

$userGroupB2C = $api->userGroup()->create([
    "name" => "b2c",
    "shop_id" => $shopEurope,
]);


//--------------------------------------------
// users
//--------------------------------------------

$userLing = $api->user()->create([
    'shop_id' => $shopEurope,
    'email' => "ling",
    'pass' => E::passEncrypt("poupou"),
    'date_creation' => "2017-05-28 09:49:44",
    'mobile' => "0612457865",
    'phone' => "0247609841",
    'newsletter' => "1",
    'active' => "1",
]);


$api->userHasUserGroup()->create([
    "user_id" => $userLing,
    "user_group_id" => $userGroupB2B,
]);


$userLingUsa = $api->user()->create([
    'shop_id' => $shopUsa,
    'email' => "ling",
    'pass' => E::passEncrypt("poupou"),
    'date_creation' => "2017-05-28 09:49:44",
    'mobile' => "0612457865",
    'phone' => "0247609841",
    'newsletter' => "1",
    'active' => "1",
]);


$api->userHasUserGroup()->create([
    "user_id" => $userLingUsa,
    "user_group_id" => $userGroupB2B,
]);


//--------------------------------------------
// discounts
//--------------------------------------------
$target = "priceWithoutTax";
$target = "priceWithTax";
$discount50Destockage = $api->discount()->create([
    "user_group_id" => null,
    "currency_id" => null,
    "date_start" => null,
    "date_end" => null,
    "procedure_type" => "percent",
    "procedure_operand" => "50",
    "target" => $target,
    "shop_id" => $shopEurope,
]);

$discountMinusHalfEuro = $api->discount()->create([
    "user_group_id" => null,
    "currency_id" => null,
    "date_start" => null,
    "date_end" => null,
    "procedure_type" => "amount",
    "procedure_operand" => "0.50",
    "target" => $target,
    "shop_id" => $shopEurope,
]);

$discountMinus2Euros = $api->discount()->create([
    "user_group_id" => null,
    "currency_id" => null,
    "date_start" => null,
    "date_end" => null,
    "procedure_type" => "amount",
    "procedure_operand" => "2",
    "target" => $target,
    "shop_id" => $shopEurope,
]);

$discountMinus10Percent = $api->discount()->create([
    "user_group_id" => null,
    "currency_id" => null,
    "date_start" => null,
    "date_end" => null,
    "procedure_type" => "percent",
    "procedure_operand" => "10",
    "target" => $target,
    "shop_id" => $shopEurope,
]);

$discountTestMinus1PointFiveEuro = $api->discount()->create([
    "user_group_id" => $userGroupB2B,
    "currency_id" => null,
    "date_start" => null,
    "date_end" => null,
    "procedure_type" => "amount",
    "procedure_operand" => "1.5",
    "target" => "priceWithoutTax",
    "shop_id" => $shopEurope,
]);


//--------------------------------------------
// discounts lang
//--------------------------------------------
$api->discountLang()->create([
    "discount_id" => $discount50Destockage,
    "lang_id" => $langFrench,
    "label" => "Déstockage 50%",
]);


$api->discountLang()->create([
    "discount_id" => $discountMinusHalfEuro,
    "lang_id" => $langFrench,
    "label" => "remise charitable",
]);

$api->discountLang()->create([
    "discount_id" => $discountMinus2Euros,
    "lang_id" => $langFrench,
    "label" => "-2 euros",
]);

$api->discountLang()->create([
    "discount_id" => $discountMinus10Percent,
    "lang_id" => $langFrench,
    "label" => "-10%",
]);

$api->discountLang()->create([
    "discount_id" => $discountTestMinus1PointFiveEuro,
    "lang_id" => $langFrench,
    "label" => "-1.5€",
]);


//--------------------------------------------
// product has discount
//--------------------------------------------
$api->productHasDiscount()->create([
    "product_id" => $productKettleBell_4,
    "discount_id" => $discount50Destockage,
    "order_phase" => 0,
    "active" => 1,
]);

$api->productHasDiscount()->create([
    "product_id" => $productKettleBell_4,
    "discount_id" => $discountMinusHalfEuro,
    "order_phase" => 1,
    "active" => 1,
]);

//--------------------------------------------
// product card has discount
//--------------------------------------------
$api->productCardHasDiscount()->create([
    "product_card_id" => $cardKettleBell,
    "discount_id" => $discountMinus2Euros,
    "order_phase" => 2,
    "active" => 1,
]);

$api->productCardHasDiscount()->create([
    "product_card_id" => $cardBallonPaille,
    "discount_id" => $discountTestMinus1PointFiveEuro,
    "order_phase" => 0,
    "active" => 1,
]);


//--------------------------------------------
// category has discount
//--------------------------------------------
$api->categoryHasDiscount()->create([
    "category_id" => $categoryPilates,
    "discount_id" => $discountMinus10Percent,
    "order_phase" => 3,
    "active" => 1,
]);


//--------------------------------------------
// order
//--------------------------------------------

//--------------------------------------------
// order status
//--------------------------------------------
$statuses = [
    'orderPlaced' => [
        "Order placed",
        "orange",
    ],
    'paymentAccepted' => [
        "Payment accepted",
        "red",
    ],
    'shipped' => [
        "Shipped",
        "purple",
    ],
];
foreach ($statuses as $code => $info) {

    $status = $api->orderStatus()->create([
        'code' => $code,
        'color' => $info[1],
        'shop_id' => $shopEurope,
    ]);

    $api->orderStatusLang()->create([
        "order_status_id" => $status,
        "lang_id" => $langFrench,
        "label" => $info[0],
    ]);
    $api->orderStatusLang()->create([
        "order_status_id" => $status,
        "lang_id" => $langEnglish,
        "label" => $info[0],
    ]);
}


//--------------------------------------------
// coupon
//--------------------------------------------
$couponABC = $api->coupon()->create([
    "code" => 'abc',
    "active" => '1',
    "mode" => '',
    "priority" => 0,
    "shop_id" => $shopEurope,
]);

$couponDEF = $api->coupon()->create([
    "code" => 'def',
    "active" => '1',
    "mode" => '',
    "priority" => 0,
    "shop_id" => $shopEurope,
]);

$couponGHI = $api->coupon()->create([
    "code" => 'ghi',
    "active" => '1',
    "mode" => '',
    "priority" => 0,
    "shop_id" => $shopEurope,
]);


//--------------------------------------------
// coupon lang
//--------------------------------------------
$api->couponLang()->create([
    "coupon_id" => $couponABC,
    "lang_id" => $langFrench,
    "label" => "coupon abc",
]);
$api->couponLang()->create([
    "coupon_id" => $couponDEF,
    "lang_id" => $langFrench,
    "label" => "coupon def",
]);
$api->couponLang()->create([
    "coupon_id" => $couponGHI,
    "lang_id" => $langFrench,
    "label" => "coupon ghi",
]);

//--------------------------------------------
// cart discount
//--------------------------------------------
$cdiscountMinus2Euros = $api->cartDiscount()->create([
    'target' => 'beforeShipping',
    'procedure_type' => 'relativeAmount',
    'procedure_operand' => '-2',
    'shop_id' => $shopEurope,
]);
$cdiscountMinus3Euros = $api->cartDiscount()->create([
    'target' => 'beforeShipping',
    'procedure_type' => 'relativeAmount',
    'procedure_operand' => '-3',
    'shop_id' => $shopEurope,
]);
$cdiscountMinus4Euros = $api->cartDiscount()->create([
    'target' => 'beforeShipping',
    'procedure_type' => 'relativeAmount',
    'procedure_operand' => '-4',
    'shop_id' => $shopEurope,
]);

//--------------------------------------------
// cart discount lang
//--------------------------------------------
$api->cartDiscountLang()->create([
    "cart_discount_id" => $cdiscountMinus2Euros,
    "lang_id" => $langFrench,
    "label" => "Coupon ABC: Minus 2 Euros",
]);
$api->cartDiscountLang()->create([
    "cart_discount_id" => $cdiscountMinus3Euros,
    "lang_id" => $langFrench,
    "label" => "Coupon DEF: Minus 3 Euros",
]);
$api->cartDiscountLang()->create([
    "cart_discount_id" => $cdiscountMinus4Euros,
    "lang_id" => $langFrench,
    "label" => "Coupon GHI: Minus 4 Euros",
]);


//--------------------------------------------
// coupon has cart discount
//--------------------------------------------
$api->couponHasCartDiscount()->create([
    "coupon_id" => $couponABC,
    "cart_discount_id" => $cdiscountMinus2Euros,
]);
$api->couponHasCartDiscount()->create([
    "coupon_id" => $couponABC,
    "cart_discount_id" => $cdiscountMinus3Euros,
]);
$api->couponHasCartDiscount()->create([
    "coupon_id" => $couponDEF,
    "cart_discount_id" => $cdiscountMinus4Euros,
]);


//--------------------------------------------
// country
// country lang
//--------------------------------------------
$countries = [];
include __DIR__ . "/countries.php";
$countryFra = 0;
foreach ($countries as $iso => $v) {
    $id = $api->country()->create([
        'iso_code' => $iso,
    ]);

    $api->countryLang()->create([
        'country_id' => $id,
        'lang_id' => $langFrench,
        'label' => $v['fra'],
    ]);
    $api->countryLang()->create([
        'country_id' => $id,
        'lang_id' => $langEnglish,
        'label' => $v['eng'],
    ]);
    if ('FR' === $iso) {
        $countryFra = $id;
    }
}


//--------------------------------------------
// address
//--------------------------------------------
$addressLing1 = $api->address()->create([
    'first_name' => 'pierre',
    'last_name' => 'Lafitte',
    'phone' => '06 83 27 59 32',
    'address' => '6 rue port feu hugon',
    'city' => 'Tours',
    'postcode' => '37000',
    'supplement' => '',
    'active' => '1',
    'country_id' => $countryFra,
]);

$addressLing2 = $api->address()->create([
    'first_name' => 'maurice',
    'last_name' => 'Bouchez',
    'phone' => '02 47 60 98 41',
    'address' => '2 avenue du maréchal leclerc',
    'city' => 'Chartres',
    'postcode' => '28000',
    'supplement' => '',
    'active' => '1',
    'country_id' => $countryFra,
]);

$addressShopEurope = $api->address()->create([
    'first_name' => 'Leaderfit',
    'last_name' => '',
    'phone' => '02 47 52 66 01',
    'city' => 'Tours',
    'postcode' => '37000',
    'address' => '9 rue du général mocquery',
    'supplement' => '',
    'active' => '1',
    'country_id' => $countryFra,
]);


//--------------------------------------------
// user has address
//--------------------------------------------
$api->userHasAddress()->create([
    'user_id' => $userLing,
    'address_id' => $addressLing1,
    'type' => 'billing',
    'order' => '0',
]);

$api->userHasAddress()->create([
    'user_id' => $userLing,
    'address_id' => $addressLing1,
    'type' => 'shipping',
    'order' => '0',
]);

$api->userHasAddress()->create([
    'user_id' => $userLing,
    'address_id' => $addressLing2,
    'type' => 'shipping',
    'order' => '1',
]);


//--------------------------------------------
// carrier
//--------------------------------------------
$carrierDemo = $api->carrier()->create([
    'name' => 'demo',
]);

$carrierUps = $api->carrier()->create([
    'name' => 'ups',
]);


//--------------------------------------------
// shop has carrier
//--------------------------------------------
$api->shopHasCarrier()->create([
    'shop_id' => $shopEurope,
    'carrier_id' => $carrierDemo,
    'priority' => '0',
]);

$api->shopHasCarrier()->create([
    'shop_id' => $shopEurope,
    'carrier_id' => $carrierUps,
    'priority' => '1',
]);


//--------------------------------------------
// shop has address
//--------------------------------------------
$api->shopHasAddress()->create([
    'shop_id' => $shopEurope,
    'address_id' => $addressShopEurope,
    'type' => "physical",
    'order' => 0,
]);


//--------------------------------------------
// payment method
//--------------------------------------------
$paymentCreditCardWallet = $api->paymentMethod()->create([
    'name' => 'credit_card_wallet',
]);

$paymentPaypal = $api->paymentMethod()->create([
    'name' => 'paypal',
]);


//--------------------------------------------
// shop has payment method
//--------------------------------------------
$api->shopHasPaymentMethod()->create([
    "shop_id" => $shopEurope,
    "payment_method_id" => $paymentCreditCardWallet,
    "order" => 0,
    "configuration" => serialize([]),
]);


$api->shopHasPaymentMethod()->create([
    "shop_id" => $shopEurope,
    "payment_method_id" => $paymentPaypal,
    "order" => 1,
    "configuration" => serialize([]),
]);






