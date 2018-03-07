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

$choice_tax_id = QuickPdo::fetchAll("select id, concat(id, \". \", amount) as label from ek_tax", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_tax_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_tax_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'tax_group_id',
    'tax_id',
];

$tax_id = (array_key_exists("tax_id", $_GET)) ? $_GET['tax_id'] : null;
$tax_group_id = (array_key_exists("tax_group_id", $_GET)) ? $_GET['tax_group_id'] : null;



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
    'title' => "tax group-tax",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_tax_group_has_tax")
        ->addControl(SokoChoiceControl::create()
            ->setName("tax_group_id")
            ->setLabel("Tax group id")
            ->setProperties([
                'readonly' => (null !== $tax_group_id),
            ])
            ->setValue($tax_group_id)
            ->setChoices($choice_tax_group_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("tax_id")
            ->setLabel("Tax id")
            ->setProperties([
                'readonly' => (null !== $tax_id),
            ])
            ->setValue($tax_id)
            ->setChoices($choice_tax_id))
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        )
        ->addControl(SokoInputControl::create()
            ->setName("mode")
            ->setLabel("Mode")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_tax_group_has_tax"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $tax_group_id, $tax_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_tax_group_has_tax", [
				"tax_group_id" => $fData["tax_group_id"],
				"tax_id" => $fData["tax_id"],
				"order" => $fData["order"],
				"mode" => $fData["mode"],

            ], '', $ric);
            $form->addNotification("Le/la tax group-tax a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_tax_group_has_tax", [
				"order" => $fData["order"],
				"mode" => $fData["mode"],

            ], [
				["tax_group_id", "=", $tax_group_id],
				["tax_id", "=", $tax_id],
            
            ]);
            $form->addNotification("Le/la tax group-tax a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
