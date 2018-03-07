<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$team_id = MorphicHelper::getFormContextValue("team_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `tm_team_has_contact` h 
inner join tm_contact c on c.id=h.contact_id 
inner join tm_team t on t.id=h.team_id
where h.team_id=$team_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Team has contacts",
    'table' => 'tm_team_has_contact',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'tm_team_has_contact',
    "headers" => [
        'team_id' => 'Team id',
        'contact_id' => 'Contact id',
        'contact' => 'Contact',
        '_action' => '',
    ],
    "headersVisibility" => [
        'team_id' => false,
        'contact_id' => false,
    ],
    "realColumnMap" => [
        'contact' => [
            'c.name',
            'c.email',
            'c.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.team_id',
        'h.contact_id',
        'concat(c.id, ". ", c.name) as contact',
    ],
    "ric" => [
        'team_id',
        'contact_id',
    ],
    
    "formRouteExtraVars" => [               
        "team_id" => $team_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TmTeamHasContact_List",    
    'context' => $context,
];


