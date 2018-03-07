<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;


$langId = EkomNullosUser::getEkomValue("lang_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product attribute",
    'table' => 'ek_product_attribute',
    'viewId' => 'product_attribute',
    'headers' => [
        'id' => "Id",
        'name' => 'Name',
        '_action' => '',
    ],
//    'realColumnMap' => [
//        'id' => "t.id",
//    ],
    'querySkeleton' => '
select %s 
from ek_product_attribute',
    'queryCols' => [
        'id',
        'name',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductAttribute_List",
//    'defaultFormLinkPrefix' => E::link("NullosAdmin_Ekom_Tax_List") . "?",
    //
//    'rowActionUpdateRicAdaptor' => [
//        'id' => "tax_id",
//    ],
];