<?php


use Module\Ekom\Back\User\EkomNullosUser;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$langId = (int)EkomNullosUser::getEkomValue("lang_id");
$id = (array_key_exists('product_id', $_GET)) ? (int)$_GET['product_id'] : 0;


$q = "select %s 
from ek_shop_has_product h
inner join ek_product p on p.id=h.product_id
inner join ek_product_lang pl on pl.product_id=p.id 
inner join ek_seller s on s.id=h.seller_id 
inner join ek_product_type t on t.id=h.product_type_id
left join ek_manufacturer m on m.id=h.manufacturer_id
where 
h.shop_id=$shopId 
and pl.lang_id=$langId
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop products",
    'table' => 'ek_shop_has_product',
    'viewId' => 'shop_has_product',
    'headers' => [
//        'shop_id' => 'Shop id',
        'product_id' => 'Product id',
        'product' => 'Product',
        'reference' => 'Reference',
        'price' => 'Price',
        'wholesale_price' => 'Wholesale price',
        'quantity' => 'Quantity',
        'active' => 'Active',
        '_discount_badge' => 'Discount badge',
        'seller_id' => 'Seller id',
        'seller' => 'Seller',
        'product_type_id' => 'Product type id',
        'product_type' => 'Product type',
        '_popularity' => '_Popularity',
        'codes' => 'Codes',
        'manufacturer_id' => 'Manufacturer id',
        'manufacturer' => 'Manufacturer',
        '_action' => '',
    ],
    "headersVisibility" => [
        "product_id" => false,
        "seller_id" => false,
        "product_type_id" => false,
        "manufacturer_id" => false,
    ],
    "realColumnMap" => [
        "product" => ["p.id", "p.reference", "pl.label"],
        "seller" => "s.name",
        "product_type" => "t.name",
        "manufacturer" => "m.name",
        "price" => "h.price",
        "reference" => "h.reference",
    ],
    'querySkeleton' => $q,
    'queryCols' => [
        'h.product_id',
        'concat (
h.price,
case when p.price is null or p.price=0
then ""
else
concat(" (def=", p.price, ")")
end
        
        ) as price',
        'h.wholesale_price',
        'h.quantity',
        'h.active',
        'h._discount_badge',
        'h.seller_id',
        'h.product_type_id',
        'concat (
h.reference,
case when p.reference=""
then ""
else
concat(" (def=", p.reference, ")")
end        
        ) as reference',
        'h._popularity',
        'h.codes',
        'h.manufacturer_id',
        'concat (p.id, ". ", pl.label, " (", p.reference, ")") as product',
        'concat (s.id, ". ", s.name) as seller',
        'concat (t.id, ". ", t.name) as product_type',
        'concat (m.id, ". ", m.name) as manufacturer',
    ],
    'ric' => [
//        'shop_id',
        'product_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ShopHasProduct_List",
];