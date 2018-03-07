<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_event_has_course_has_participant` h
inner join ekev_event_has_course `e` on `e`.id=h.event_has_course_id
inner join ekev_participant `p` on `p`.id=h.participant_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "event-course-participants",
    'table' => 'ekev_event_has_course_has_participant',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_event_has_course_has_participant',
    "headers" => [
        'id' => 'Id',
        'event_has_course_id' => 'Event has course id',
        'participant_id' => 'Participant id',
        'sponsor_user_id' => 'Sponsor user id',
        'datetime' => 'Datetime',
        'event-course' => 'Event-course',
        'participant' => 'Participant',
        '_action' => '',
    ],
    "headersVisibility" => [
        'event_has_course_id' => false,
        'participant_id' => false,
    ],
    "realColumnMap" => [
        'event-course' => [
            'e.id',
            'e.start_time',
        ],
        'participant' => [
            'p.id',
            'p.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.event_has_course_id',
        'h.participant_id',
        'h.sponsor_user_id',
        'h.datetime',
        'concat( e.id, ". ", e.start_time ) as `event-course`',
        'concat( p.id, ". ", p.email ) as `participant`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List",    
    'context' => $context,
];


