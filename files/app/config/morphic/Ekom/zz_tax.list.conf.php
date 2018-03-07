<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;


$langId = EkomNullosUser::getEkomValue("lang_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Taxes",
    'table' => 'ek_tax',
    'viewId' => 'tax',
    'headers' => [
        'id' => "Id",
        'amount' => 'Amount',
        '_action' => '',
    ],
//    'realColumnMap' => [
//        'id' => "t.id",
//    ],
    'querySkeleton' => '
select %s 
from ek_tax',
    'queryCols' => [
        'id',
        'amount',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Tax_List",
//    'defaultFormLinkPrefix' => E::link("NullosAdmin_Ekom_Tax_List") . "?",
    //
//    'rowActionUpdateRicAdaptor' => [
//        'id' => "tax_id",
//    ],
];