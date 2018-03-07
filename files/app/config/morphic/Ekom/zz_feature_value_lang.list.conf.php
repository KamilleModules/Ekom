<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // userId


$q = "
select %s 
from ek_feature_value_lang cl 
inner join ek_lang l on l.id=cl.lang_id
where cl.feature_value_id=$id
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product feature value translations",
    'table' => 'ek_feature_value_lang',
    'viewId' => 'feature_value_lang',
    'queryCols' => [
        'cl.feature_value_id',
        'cl.lang_id',
        'l.iso_code as lang',
        'cl.value',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'feature_value_id' => "Product feature value id",
        'lang_id' => "Lang id",
        'lang' => "Lang",
        'value' => "Value",
        '_action' => '',
    ],
    'headersVisibility' => [
        'lang_id' => false,
    ],
    'realColumnMap' => [
        'feature_value_id' => "cl.feature_value_id",
        'lang_id' => "cl.lang_id",
        'lang' => "l.iso_code",
        'value' => "cl.value",
    ],
    'ric' => [
        'feature_value_id',
        'lang_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_FeatureValueLang_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


