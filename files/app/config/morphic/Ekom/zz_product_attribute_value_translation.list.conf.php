<?php


use Module\Ekom\Api\Layer\ProductAttributeLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Utils\E;


$product_attribute_valueId = array_key_exists("id", $_GET) ? (int)$_GET['id'] : 0;
if (0 === $product_attribute_valueId) {
    if (array_key_exists("product_attribute_value_id", $_GET)) {
        $product_attribute_valueId = (int)$_GET['product_attribute_value_id'];
    }
}

if (0 === $product_attribute_valueId) {
    if (array_key_exists("product_attribute_value_id", $context)) {
        $product_attribute_valueId = (int)$context['product_attribute_value_id'];
    }
    elseif (array_key_exists("id", $context)) {
        $product_attribute_valueId = (int)$context['id'];
    }
}


if (0 !== $product_attribute_valueId) {


    $q = "select %s 
from ek_product_attribute_value_lang l 
inner join ek_product_attribute_value t on t.id=l.product_attribute_value_id
inner join ek_lang a on a.id=l.lang_id";
    if (0 !== $product_attribute_valueId) {
        $q .= "
where t.id=$product_attribute_valueId    
    ";
    }


    $label = "Product attribute value translations";

    $name = ProductAttributeLayer::getProductAttributeValueById($product_attribute_valueId);
    $label .= " for \"$name\"";
    $link = E::link("NullosAdmin_Ekom_ProductAttributeValueTranslation_List") . "?id=" . $product_attribute_valueId;


    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => $label,
        'table' => 'ek_product_attribute_value_lang',
        'viewId' => 'product_attribute_value_translation',
        'headers' => [
            'product_attribute_value_id' => "Product attribute value id",
            'lang_id' => 'Lang Id',
            'value' => "Value",
            'label' => 'Label',
            'lang' => 'Lang',
            '_action' => '',
        ],
        'headersVisibility' => [
            'product_attribute_value_id' => false,
            'lang_id' => false,
        ],
        'realColumnMap' => [
            'product_attribute_value_id' => 't.id',
            'lang_id' => 'l.id',
            'label' => 'l.value',
            'lang_iso_code' => 'a.iso_code',
            'lang' => 'a.iso_code',
            'product_attribute_value' => 't.value',
        ],
        'querySkeleton' => $q,
        'queryCols' => [
            'l.product_attribute_value_id',
            'l.lang_id',
            'l.value as label',
            't.value',
            'concat(a.iso_code, " (", a.id, ")") as lang',
        ],
        'ric' => [
            'product_attribute_value_id',
            'lang_id',
        ],
        /**
         * formRoute is just a helper, it will be used to generate the rowActions key.
         */
        'formRoute' => "NullosAdmin_Ekom_ProductAttributeValueTranslation_List",
        'context' => $context,
        'buttons' => [
            [
                'link' => $link,
                'text' => "Ajouter une traduction pour cette valeur d'attribut de produit",
            ],
        ],
    ];


} else {
    throw new \Exception("Product attribute value id not set");
}
