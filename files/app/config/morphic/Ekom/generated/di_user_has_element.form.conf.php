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

$choice_element_id = QuickPdo::fetchAll("select id, concat(id, \". \", type) as label from di_element", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from di_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'user_id',
    'element_id',
];

$element_id = (array_key_exists("element_id", $_GET)) ? $_GET['element_id'] : null;
$user_id = (array_key_exists("user_id", $_GET)) ? $_GET['user_id'] : null;



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
    'title' => "user-element",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_user_has_element")
        ->addControl(SokoChoiceControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => (null !== $user_id),
            ])
            ->setValue($user_id)
            ->setChoices($choice_user_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("element_id")
            ->setLabel("Element id")
            ->setProperties([
                'readonly' => (null !== $element_id),
            ])
            ->setValue($element_id)
            ->setChoices($choice_element_id))
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_completed")
            ->setLabel("Date_completed")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("value")
            ->setLabel("Value")
            ->setType("textarea")
        ),
    'feed' => MorphicHelper::getFeedFunction("di_user_has_element"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $user_id, $element_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("di_user_has_element", [
				"user_id" => $fData["user_id"],
				"element_id" => $fData["element_id"],
				"date_completed" => $fData["date_completed"],
				"value" => $fData["value"],

            ], '', $ric);
            $form->addNotification("Le/la user-element a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("di_user_has_element", [
				"date_completed" => $fData["date_completed"],
				"value" => $fData["value"],

            ], [
				["user_id", "=", $user_id],
				["element_id", "=", $element_id],
            
            ]);
            $form->addNotification("Le/la user-element a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
