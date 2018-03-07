<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product features",
    'table' => 'ek_feature',
    'viewId' => 'feature',
    'queryCols' => [
        'f.id',
        'fl.name',
        'l.iso_code as lang',
    ],
    'querySkeleton' => "
select %s 
from ek_feature f 
left join ek_feature_lang fl on fl.feature_id=f.id
left join ek_lang l on l.id=fl.lang_id
",
    'headers' => [
        'id' => "Id",
        'name' => "Name",
        'lang' => "Lang",
        '_action' => '',
    ],
    'headersVisibility' => [],
    'realColumnMap' => [
        'id' => 'f.id',
        'name' => 'fl.name',
        'lang' => 'l.iso_code',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Feature_List",
];


