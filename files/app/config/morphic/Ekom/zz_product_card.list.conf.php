<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product cards",
    'table' => 'ek_product_card',
    'viewId' => 'product_card',
    'queryCols' => [
        'c.id',
        'cl.label',
        'l.iso_code as lang',
    ],
    'querySkeleton' => "
select %s 
from ek_product_card c 
left join ek_product_card_lang cl on cl.product_card_id=c.id
left join ek_lang l on l.id=cl.lang_id
",
    'headers' => [
        'id' => "Id",
        'label' => "Label",
        'lang' => "Lang",
        '_action' => '',
    ],
    'headersVisibility' => [],
    'realColumnMap' => [
        'id' => 'c.id',
        'label' => 'cl.label',
        'lang' => 'l.iso_code',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductCard_List",
];


