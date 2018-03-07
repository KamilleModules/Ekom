<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$training_session_id = MorphicHelper::getFormContextValue("training_session_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ektra_training_session_has_participant` h 
inner join ektra_participant p on p.id=h.participant_id 
inner join ektra_training_session t on t.id=h.training_session_id
where h.training_session_id=$training_session_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Training session has participants",
    'table' => 'ektra_training_session_has_participant',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_training_session_has_participant',
    "headers" => [
        'training_session_id' => 'Training session id',
        'participant_id' => 'Participant id',
        'participant' => 'Participant',
        '_action' => '',
    ],
    "headersVisibility" => [
        'training_session_id' => false,
        'participant_id' => false,
    ],
    "realColumnMap" => [
        'participant' => [
            '.email',
            '.first_name',
            '.last_name',
            '.address',
            '.city',
            '.postcode',
            '.phone',
            '.birthday',
            '.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.training_session_id',
        'h.participant_id',
        'concat(p.id, ". ", p.first_name) as participant',
    ],
    "ric" => [
        'training_session_id',
        'participant_id',
    ],
    
    "formRouteExtraVars" => [               
        "training_session_id" => $training_session_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List",    
    'context' => $context,
];


