<?php

use Kamille\Utils\Morphic\Helper\MorphicHelper;


$q = "select %s from `ekev_event_has_course` h 
inner join ekev_course c on c.id=h.course_id 
inner join ekev_event e on e.id=h.event_id 
inner join ekev_presenter_group p on p.id=h.presenter_group_id
";


//--------------------------------------------
// DO WE USE THE CHILD PATTERN HERE?
//--------------------------------------------
/**
 * parentKeys:
 * if not null, means there is a parent driving...
 */
$parentKeys = (array_key_exists('_parentKeys', $context)) ? $context['_parentKeys'] : null;
$parentValues = [];
if ($parentKeys) {
    $avatar = MorphicHelper::getFormContextValue("avatar", $context);
    $q .= " where ";
    foreach ($parentKeys as $key) {
        $value = MorphicHelper::getFormContextValue($key, $context);
        $q .= "h.$key=$value";
        $parentValues[$key] = $value;
    }
}


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Event has courses",
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
        'event' => 'Event',
        'course' => 'Course',
        'presenter_group' => 'Presenter group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'event_id' => false,
        'course_id' => false,
        'presenter_group_id' => false,
    ],
    "realColumnMap" => [
        'event' => [
            'e.name',
            'e.id',
        ],
        'course' => [
            'c.name',
            'c.id',
        ],
        'presenter_group' => [
            'p.name',
            'p.id',
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
        'concat(e.id, ". ", e.name) as event',
        'concat(c.id, ". ", c.name) as course',
        'concat(p.id, ". ", p.name) as presenter_group',
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


