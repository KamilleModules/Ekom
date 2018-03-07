<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_training_session_has_participant` h
inner join ektra_participant `p` on `p`.id=h.participant_id
inner join ektra_training_session `t` on `t`.id=h.training_session_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "training session-participants",
    'table' => 'ektra_training_session_has_participant',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_training_session_has_participant',
    "headers" => [
        'training_session_id' => 'Training session id',
        'participant_id' => 'Participant id',
        'participant' => 'Participant',
        'training_session' => 'Training session',
        '_action' => '',
    ],
    "headersVisibility" => [
        'participant_id' => false,
        'training_session_id' => false,
    ],
    "realColumnMap" => [
        'participant' => [
            'p.id',
            'p.first_name',
        ],
        'training_session' => [
            't.id',
            't.active',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.training_session_id',
        'h.participant_id',
        'concat( p.id, ". ", p.first_name ) as `participant`',
        'concat( t.id, ". ", t.active ) as `training_session`',
    ],
    "ric" => [
        'training_session_id',
        'participant_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List",    
    'context' => $context,
];


