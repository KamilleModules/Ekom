<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;


$langId = EkomNullosUser::getEkomValue("lang_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product attribute value",
    'table' => 'ek_product_attribute_value',
    'viewId' => 'product_attribute_value',
    'headers' => [
        'id' => "Id",
        'value' => 'Value',
        '_action' => '',
    ],
//    'realColumnMap' => [
//        'id' => "t.id",
//    ],
    'querySkeleton' => '
select %s 
from ek_product_attribute_value',
    'queryCols' => [
        'id',
        'value',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductAttributeValue_List",
//    'defaultFormLinkPrefix' => E::link("NullosAdmin_Ekom_Tax_List") . "?",
    //
//    'rowActionUpdateRicAdaptor' => [
//        'id' => "tax_id",
//    ],
];