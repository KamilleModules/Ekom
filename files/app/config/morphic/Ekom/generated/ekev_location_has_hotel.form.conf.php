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

// inferred data (can be overridden by fkeys)
$shop_id = EkomNullosUser::getEkomValue("shop_id");
$lang_id = EkomNullosUser::getEkomValue("lang_id");
$currency_id = EkomNullosUser::getEkomValue("currency_id");

$choice_hotel_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ekev_hotel", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_location_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ekev_location", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'location_id',
    'hotel_id',
];

$hotel_id = (array_key_exists("hotel_id", $_GET)) ? $_GET['hotel_id'] : null;
$location_id = (array_key_exists("location_id", $_GET)) ? $_GET['location_id'] : null;



$avatar = (array_key_exists("avatar", $context)) ? $context['avatar'] : null;

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
    'title' => "location-hotel",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_location_has_hotel")
        ->addControl(SokoChoiceControl::create()
            ->setName("location_id")
            ->setLabel("Location id")
            ->setProperties([
                'readonly' => (null !== $location_id),
            ])
            ->setValue($location_id)
            ->setChoices($choice_location_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("hotel_id")
            ->setLabel("Hotel id")
            ->setProperties([
                'readonly' => (null !== $hotel_id),
            ])
            ->setValue($hotel_id)
            ->setChoices($choice_hotel_id)),
    'feed' => MorphicHelper::getFeedFunction("ekev_location_has_hotel"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $location_id, $hotel_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ekev_location_has_hotel", [
				"location_id" => $fData["location_id"],
				"hotel_id" => $fData["hotel_id"],

            ], '', $ric);
            $form->addNotification("Le/la location-hotel a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ekev_location_has_hotel", [

            ], [
				["location_id", "=", $location_id],
				["hotel_id", "=", $hotel_id],
            
            ]);
            $form->addNotification("Le/la location-hotel a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
