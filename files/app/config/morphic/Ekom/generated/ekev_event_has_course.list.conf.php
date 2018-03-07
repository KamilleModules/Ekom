<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_event_has_course` h
inner join ekev_course `c` on `c`.id=h.course_id
inner join ekev_event `e` on `e`.id=h.event_id
inner join ekev_presenter_group `p` on `p`.id=h.presenter_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "event-courses",
    'table' => 'ekev_event_has_course',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_event_has_course',
    "headers" => [
        'id' => 'Id',
        'event_id' => 'Event id',
        'course_id' => 'Course id',
        'date' => 'Date',
        'start_time' => 'Start time',
        'end_time' => 'End time',
        'presenter_group_id' => 'Presenter group id',
        'capacity' => 'Capacity',
        'course' => 'Course',
        'event' => 'Event',
        'presenter_group' => 'Presenter group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'course_id' => false,
        'event_id' => false,
        'presenter_group_id' => false,
    ],
    "realColumnMap" => [
        'course' => [
            'c.id',
            'c.name',
        ],
        'event' => [
            'e.id',
            'e.name',
        ],
        'presenter_group' => [
            'p.id',
            'p.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.event_id',
        'h.course_id',
        'h.date',
        'h.start_time',
        'h.end_time',
        'h.presenter_group_id',
        'h.capacity',
        'concat( c.id, ". ", c.name ) as `course`',
        'concat( e.id, ". ", e.name ) as `event`',
        'concat( p.id, ". ", p.name ) as `presenter_group`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevEventHasCourse_List",    
    'context' => $context,
];


