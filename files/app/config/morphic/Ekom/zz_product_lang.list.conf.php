<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // userId


$q = "
select %s 
from ek_product_lang cl 
inner join ek_lang l on l.id=cl.lang_id
where cl.product_id=$id
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product translations",
    'table' => 'ek_product_lang',
    'viewId' => 'product_lang',
    'queryCols' => [
        'cl.product_id',
        'cl.lang_id',
        'cl.label',
        'l.iso_code as lang',
        'cl.description',
        'cl.meta_title',
        'cl.meta_description',
        'cl.meta_keywords',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'product_id' => "Product id",
        'lang_id' => "Lang id",
        'lang' => "Lang",
        'label' => "Label",
        'description' => "Description",
        'meta_title' => "Meta title",
        'meta_description' => "Meta description",
        'meta_keywords' => "Meta keywords",
        '_action' => '',
    ],
    'headersVisibility' => [
        'lang_id' => false,
    ],
    'realColumnMap' => [
        'product_id' => "cl.product_id",
        'lang_id' => "cl.lang_id",
        'lang' => "l.iso_code",
        'label' => "cl.label",
        'description' => "cl.description",
        'meta_title' => "cl.meta_title",
        'meta_description' => "cl.meta_description",
        'meta_keywords' => "cl.meta_keywords",
    ],
    'ric' => [
        'product_id',
        'lang_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductLang_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


