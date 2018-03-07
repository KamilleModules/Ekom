<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_course_lang` h
inner join ekev_course `c` on `c`.id=h.course_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "course langs",
    'table' => 'ekev_course_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_course_lang',
    "headers" => [
        'course_id' => 'Course id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'course' => 'Course',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'course_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'course' => [
            'c.id',
            'c.name',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.course_id',
        'h.lang_id',
        'h.label',
        'concat( c.id, ". ", c.name ) as `course`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'course_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevCourseLang_List",    
    'context' => $context,
];


