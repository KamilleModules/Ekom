<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product feature values",
    'table' => 'ek_feature_value',
    'viewId' => 'feature_value',
    'queryCols' => [
        'f.id',
        'concat (fel.feature_id, ". ", fel.name) as feature',
        'fl.value',
        'l.iso_code as lang',
    ],
    'querySkeleton' => "
select %s 
from ek_feature_value f 
left join ek_feature_value_lang fl on fl.feature_value_id=f.id
left join ek_lang l on l.id=fl.lang_id
left join ek_feature_lang fel on fel.feature_id=f.feature_id
",
    'headers' => [
        'id' => "Id",
        'feature' => "Feature",
        'value' => "Value",
        'lang' => "Lang",
        '_action' => '',
    ],
    'headersVisibility' => [],
    'realColumnMap' => [
        'id' => 'f.id',
        'feature' => 'fel.name',
        'value' => 'fl.value',
        'lang' => 'l.iso_code',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_FeatureValue_List",
];


