<?php


use Module\Ekom\Back\User\EkomNullosUser;

$langId = (int)EkomNullosUser::getEkomValue("lang_id");


$q = "select %s 
from ek_tag t 
inner join ek_lang l on l.id=t.lang_id";

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Tags",
    'table' => 'ek_tag',
    'viewId' => 'tag',
    'headers' => [
        'id' => "Id",
        'name' => 'Name',
        'lang_id' => 'Lang id',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        "lang_id" => false,
    ],
    "realColumnMap" => [
        "lang" => "l.iso_code",
    ],
    'querySkeleton' => $q,
    'queryCols' => [
        't.id',
        't.name',
        't.lang_id',
        'l.iso_code as lang',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Tag_List",
];