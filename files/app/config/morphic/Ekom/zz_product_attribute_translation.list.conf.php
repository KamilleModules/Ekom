<?php


use Module\Ekom\Api\Layer\ProductAttributeLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Utils\E;


$product_attributeId = array_key_exists("id", $_GET) ? (int)$_GET['id'] : 0;
if (0 === $product_attributeId) {
    if (array_key_exists("product_attribute_id", $_GET)) {
        $product_attributeId = (int)$_GET['product_attribute_id'];
    }
}

if (0 === $product_attributeId) {
    if (array_key_exists("product_attribute_id", $context)) {
        $product_attributeId = (int)$context['product_attribute_id'];
    }
    elseif (array_key_exists("id", $context)) {
        $product_attributeId = (int)$context['id'];
    }
}


if (0 !== $product_attributeId) {


    $q = "select %s 
from ek_product_attribute_lang l 
inner join ek_product_attribute t on t.id=l.product_attribute_id
inner join ek_lang a on a.id=l.lang_id";
    if (0 !== $product_attributeId) {
        $q .= "
where t.id=$product_attributeId    
    ";
    }


    $label = "Product attribute translations";

    $name = ProductAttributeLayer::getNameById($product_attributeId);
    $label .= " for \"$name\"";
    $link = E::link("NullosAdmin_Ekom_ProductAttributeTranslation_List") . "?id=" . $product_attributeId;


    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => $label,
        'table' => 'ek_product_attribute_lang',
        'viewId' => 'product_attribute_translation',
        'headers' => [
            'product_attribute_id' => "Product attribute id",
            'lang_id' => 'Lang Id',
            'name' => "Name",
            'label' => 'Label',
            'lang' => 'Lang',
            '_action' => '',
        ],
        'headersVisibility' => [
            'product_attribute_id' => false,
            'lang_id' => false,
        ],
        'realColumnMap' => [
            'product_attribute_id' => 't.id',
            'lang_id' => 'l.id',
            'label' => 'l.name',
            'lang_iso_code' => 'a.iso_code',
            'lang' => 'a.iso_code',
            'product_attribute' => 't.name',
        ],
        'querySkeleton' => $q,
        'queryCols' => [
            'l.product_attribute_id',
            'l.lang_id',
            'l.name as label',
            't.name',
            'concat(a.iso_code, " (", a.id, ")") as lang',
        ],
        'ric' => [
            'product_attribute_id',
            'lang_id',
        ],
        /**
         * formRoute is just a helper, it will be used to generate the rowActions key.
         */
        'formRoute' => "NullosAdmin_Ekom_ProductAttributeTranslation_List",
        'context' => $context,
        'buttons' => [
            [
                'link' => $link,
                'text' => "Ajouter une traduction pour cet attribut de produit",
            ],
        ],
    ];


} else {
    throw new \Exception("Product attribute id not set");
}
