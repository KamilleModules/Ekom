<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `di_page` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "pages",
    'table' => 'di_page',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_page',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'bg_document' => 'Bg document',
        'thumb' => 'Thumb',
        'width' => 'Width',
        'height' => 'Height',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.name',
        'h.bg_document',
        'h.thumb',
        'h.width',
        'h.height',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiPage_List",    
    'context' => $context,
];


