<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ekev_event_has_course_has_participant` h 
inner join ekev_event_has_course e on e.id=h.event_has_course_id 
inner join ekev_participant p on p.id=h.participant_id
where ";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Event has course has participants",
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
        'event_has_course' => 'Event has course',
        'participant' => 'Participant',
        '_action' => '',
    ],
    "headersVisibility" => [
        'event_has_course_id' => false,
        'participant_id' => false,
    ],
    "realColumnMap" => [
        'participant' => [
            'p.email',
            'p.first_name',
            'p.last_name',
            'p.address',
            'p.city',
            'p.postcode',
            'p.phone',
            'p.birthday',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.event_has_course_id',
        'h.participant_id',
        'h.sponsor_user_id',
        'h.datetime',
        'concat(e.id, ". ", e.start_time) as event_has_course',
        'concat(p.id, ". ", p.email) as participant',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List",    
    'context' => $context,
];


