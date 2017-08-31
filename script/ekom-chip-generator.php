<?php


use OrmTools\Util\Chip\ChipGenerator\ChipDescription;
use OrmTools\Util\Chip\ChipGenerator\ChipGenerator;

$targetDir = __DIR__ . "/../Chip/Generated";
$targetNameSpace = "Module\\Ekom\\Chip\\Generated";


$builder = ChipGenerator::create()
    ->setTargetDir($targetDir)
    ->setTargetNamespace($targetNameSpace);


$builder->newChip('product', ChipDescription::create()
    ->setTables([
        'ek_product',
        'ek_product_lang',
    ])
    ->setIgnoreColumns([
        'id',
        'product_id',
    ])
    ->setTransformerColumn('product_type_id', 'product_type')
    ->addLinkColumn('shop_product', "shop_product", "ShopProduct")
);


$builder->newChip('shopProduct', ChipDescription::create()
    ->setTables([
        'ek_shop_has_product',
        'ek_shop_has_product_lang',
    ])
    ->setIgnoreColumns([
        'product_id',
    ])
    ->setTransformerColumn('seller_id', 'seller')
);


$builder->newChip('productCard', ChipDescription::create()
    ->setTables([
        'ek_product_card',
        'ek_product_card_lang',
    ])
    ->setIgnoreColumns([
        'id',
        'product_card_id',
    ])
    ->addLinkColumn('shop_product_card', "shop_product_card", "ShopProductCard")
    ->addChildrenColumn('products', 'Product')
    ->addColumn('category')
);

$builder->newChip('shopProductCard', ChipDescription::create()
    ->setTables([
        'ek_shop_has_product_card',
        'ek_shop_has_product_card_lang',
    ])
    ->setIgnoreColumns([
        'product_card_id',
    ])
    ->setTransformerColumn('product_id', 'activeProduct', [
        'hint' => 'ProductChip',
    ])
);




