<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;




$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product groups",
    'table' => 'ek_product_group',
    'viewId' => 'product_group',
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
from ek_product_group',
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
    'formRoute' => "NullosAdmin_Ekom_ProductGroup_List",
//    'defaultFormLinkPrefix' => E::link("NullosAdmin_Ekom_Tax_List") . "?",
    //
//    'rowActionUpdateRicAdaptor' => [
//        'id' => "tax_id",
//    ],
];