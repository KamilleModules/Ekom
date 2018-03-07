<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


/**
 * @todo-ling:
 * if you put a left join instead of an inner join in the query below,
 * you get some (I suppose) ghost records that should be erased...
 * Todo: create some kind of "ghost erase task"...
 *
 */

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Products",
    'table' => 'ek_product',
    'viewId' => 'product',
    'queryCols' => [
        'p.id',
        'p.reference',
        'concat(
        cl.product_card_id,
        " (",
        cl.slug,
        ")"
        ) as card',
        'p.price',
        'p.weight',
        'p.width',
        'p.height',
        'p.depth',
    ],
    'querySkeleton' => "
select %s 
from ek_product p 
inner join ek_product_card_lang cl on cl.product_card_id=p.product_card_id
",
    'headers' => [
        'id' => "Id",
        'reference' => "Reference",
        'card' => "Card",
        'price' => "Price",
//        'weight' => "Weight",
//        'width' => "Width",
//        'height' => "Height",
//        'depth' => "Depth",
        '_action' => '',
    ],
    'headersVisibility' => [],
    'realColumnMap' => [
        'id' => 'p.id',
        'reference' => 'p.reference',
        'card' => 'cl.slug',
        'price' => 'p.weight',
        'weight' => 'p.weight',
        'width' => 'p.width',
        'height' => 'p.height',
        'depth' => 'p.depth',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Product_List",
];


