<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // userId


$q = "
select %s 
from ek_feature_lang cl 
inner join ek_lang l on l.id=cl.lang_id
where cl.feature_id=$id
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product feature translations",
    'table' => 'ek_feature_lang',
    'viewId' => 'feature_lang',
    'queryCols' => [
        'cl.feature_id',
        'cl.lang_id',
        'l.iso_code as lang',
        'cl.name',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'feature_id' => "Product feature id",
        'lang_id' => "Lang id",
        'lang' => "Lang",
        'name' => "Name",
        '_action' => '',
    ],
    'headersVisibility' => [
        'lang_id' => false,
    ],
    'realColumnMap' => [
        'feature_id' => "cl.feature_id",
        'lang_id' => "cl.lang_id",
        'lang' => "l.iso_code",
        'name' => "cl.name",
    ],
    'ric' => [
        'feature_id',
        'lang_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_FeatureLang_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


