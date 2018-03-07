<?php 





$q = "select %s from `ekev_course_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Course langs",
    'table' => 'ekev_course_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_course_lang',
    "headers" => [
        'course_id' => 'Course id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'course_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'course_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevCourseLang_List",    
];


