<?php


use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Services\XLog;
use Logger\Logger;
use Module\Ekom\Api\Link\Product\ProductLink;

//

require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";



A::quickPdoInit(); // initialize your db object, depends on the framework you are using, I'm using kamille


$shopId = 1;
$savedResults = [];
$uniqueString = date("Y-m-d H:i:s");


$link = ProductLink::create();

$idProduct = $link->savePhysicalProduct([
//
'reference' => 'produit_test3',
'weight' => '5.00',
'price' => '1000',
//
'lang' => 'fra',
'attributes' => [
["attribut test 001", 'valeur attribute test 001'],
["attribut test 2", 'valeur attribute test 2'],
],

]);

//$idProduct=1985;


$res = $link->saveShopProduct([
'shop_id' => $shopId,
'product_id' => $idProduct,
//
'price' => 1200,
'wholesale_price' => 900,
'quantity' => 200,
'active' => 1,
'seller' => 'leaderfit',
'product_type' => 'default',
//
'lang' => 'fra',
'label' => "produit test",
'description' => "Ceci est un produit test",
'slug' => "produit_test",
]);
a($res);

$res = $link->savePhysicalProductCard([
'lang' => 'fra',
'label' => 'Produit 1',
'description' => 'Description du produit 1',
'slug' => 'Description du produit 1',
'shop_id' => $shopId,
'categories' => [
'kettlebell',
'trampoline',
],
]);
$idProductCard = $res['product_card'];
a($res);


//$idProductCard = 3636;

$res = $link->saveShopProductCard([
'shop_id' => $shopId,
'product_card_id' => $idProductCard,
'lang' => 'fra',
'slug' => 'produit_1',
'active' => 1,
'product_id' => $idProduct,
]);

a($res);