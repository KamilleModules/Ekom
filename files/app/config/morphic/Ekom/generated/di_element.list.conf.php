<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `di_element` h
inner join di_page `p` on `p`.id=h.page_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "elements",
    'table' => 'di_element',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_element',
    "headers" => [
        'id' => 'Id',
        'page_id' => 'Page id',
        'type' => 'Type',
        'varname' => 'Varname',
        'pos_x' => 'Pos x',
        'pos_y' => 'Pos y',
        'width' => 'Width',
        'height' => 'Height',
        'validation' => 'Validation',
        'page' => 'Page',
        '_action' => '',
    ],
    "headersVisibility" => [
        'page_id' => false,
    ],
    "realColumnMap" => [
        'page' => [
            'p.id',
            'p.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.page_id',
        'h.type',
        'h.varname',
        'h.pos_x',
        'h.pos_y',
        'h.width',
        'h.height',
        'h.validation',
        'concat( p.id, ". ", p.name ) as `page`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiElement_List",    
    'context' => $context,
];


