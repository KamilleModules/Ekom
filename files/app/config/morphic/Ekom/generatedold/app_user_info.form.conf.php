<?php 

use QuickPdo\QuickPdo;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use SokoForm\Form\SokoFormInterface;
use SokoForm\Form\SokoForm;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use Module\Ekom\Utils\E;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\SokoForm\Control\EkomSokoDateControl;




//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'user_id',
];
$user_id = (array_key_exists('user_id', $_GET)) ? $_GET['user_id'] : null;
        
//--------------------------------------------
// UPDATE|INSERT MODE
//--------------------------------------------
$isUpdate = MorphicHelper::getIsUpdate($ric);
        
//--------------------------------------------
// FORM
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "User info",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-app_user_info")
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.user",
            ]))    
            ->setName("user_id")
            ->setLabel("User id")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("abo_leader_mail")
            ->setLabel("Abo_leader_mail")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("abo_leader_partners_mail")
            ->setLabel("Abo_leader_partners_mail")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("abo_leader_sms")
            ->setLabel("Abo_leader_sms")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("points_equipement")
            ->setLabel("Points_equipement")
        )
        ->addControl(SokoInputControl::create()
            ->setName("points_event")
            ->setLabel("Points_event")
        )
        ->addControl(SokoInputControl::create()
            ->setName("points_formation")
            ->setLabel("Points_formation")
        )
        ->addControl(SokoInputControl::create()
            ->setName("points_communication")
            ->setLabel("Points_communication")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pro_type")
            ->setLabel("Pro_type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pro_secteur")
            ->setLabel("Pro_secteur")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pro_secteur_autre")
            ->setLabel("Pro_secteur_autre")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pro_fonction")
            ->setLabel("Pro_fonction")
        )
        ->addControl(SokoInputControl::create()
            ->setName("b2b_company")
            ->setLabel("B2b_company")
        )
        ->addControl(SokoInputControl::create()
            ->setName("b2b_siret")
            ->setLabel("B2b_siret")
        )
        ->addControl(SokoInputControl::create()
            ->setName("b2b_tva")
            ->setLabel("B2b_tva")
        )
        ->addControl(SokoInputControl::create()
            ->setName("user_country")
            ->setLabel("User_country")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("alert_lf_points_catalog")
            ->setLabel("Alert_lf_points_catalog")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("app_user_info"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $user_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("app_user_info", [
				"user_id" => $fData["user_id"],
				"abo_leader_mail" => $fData["abo_leader_mail"],
				"abo_leader_partners_mail" => $fData["abo_leader_partners_mail"],
				"abo_leader_sms" => $fData["abo_leader_sms"],
				"points_equipement" => $fData["points_equipement"],
				"points_event" => $fData["points_event"],
				"points_formation" => $fData["points_formation"],
				"points_communication" => $fData["points_communication"],
				"pro_type" => $fData["pro_type"],
				"pro_secteur" => $fData["pro_secteur"],
				"pro_secteur_autre" => $fData["pro_secteur_autre"],
				"pro_fonction" => $fData["pro_fonction"],
				"b2b_company" => $fData["b2b_company"],
				"b2b_siret" => $fData["b2b_siret"],
				"b2b_tva" => $fData["b2b_tva"],
				"user_country" => $fData["user_country"],
				"alert_lf_points_catalog" => $fData["alert_lf_points_catalog"],

            ]);
            $form->addNotification("Le/la User info a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("app_user_info", [
				"abo_leader_mail" => $fData["abo_leader_mail"],
				"abo_leader_partners_mail" => $fData["abo_leader_partners_mail"],
				"abo_leader_sms" => $fData["abo_leader_sms"],
				"points_equipement" => $fData["points_equipement"],
				"points_event" => $fData["points_event"],
				"points_formation" => $fData["points_formation"],
				"points_communication" => $fData["points_communication"],
				"pro_type" => $fData["pro_type"],
				"pro_secteur" => $fData["pro_secteur"],
				"pro_secteur_autre" => $fData["pro_secteur_autre"],
				"pro_fonction" => $fData["pro_fonction"],
				"b2b_company" => $fData["b2b_company"],
				"b2b_siret" => $fData["b2b_siret"],
				"b2b_tva" => $fData["b2b_tva"],
				"user_country" => $fData["user_country"],
				"alert_lf_points_catalog" => $fData["alert_lf_points_catalog"],

            ], [
				["user_id", "=", $user_id],
            
            ]);
            $form->addNotification("Le/la User info a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


