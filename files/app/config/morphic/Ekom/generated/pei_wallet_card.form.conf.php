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

$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from ek_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
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
    'title' => "wallet card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-pei_wallet_card")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => (null !== $user_id),
            ])
            ->setValue($user_id)
            ->setChoices($choice_user_id))
        ->addControl(SokoInputControl::create()
            ->setName("type")
            ->setLabel("Type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_four_digits")
            ->setLabel("Last_four_digits")
        )
        ->addControl(SokoInputControl::create()
            ->setName("owner")
            ->setLabel("Owner")
        )
        ->addControl(SokoInputControl::create()
            ->setName("expiration_date")
            ->setLabel("Expiration_date")
        )
        ->addControl(SokoInputControl::create()
            ->setName("alias")
            ->setLabel("Alias")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default")
            ->setLabel("Is_default")
            ->setValue(1)
        ),
    'feed' => MorphicHelper::getFeedFunction("pei_wallet_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("pei_wallet_card", [
				"user_id" => $fData["user_id"],
				"type" => $fData["type"],
				"last_four_digits" => $fData["last_four_digits"],
				"owner" => $fData["owner"],
				"expiration_date" => $fData["expiration_date"],
				"alias" => $fData["alias"],
				"active" => $fData["active"],
				"is_default" => $fData["is_default"],

            ], '', $ric);
            $form->addNotification("Le/la wallet card a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("pei_wallet_card", [
				"user_id" => $fData["user_id"],
				"type" => $fData["type"],
				"last_four_digits" => $fData["last_four_digits"],
				"owner" => $fData["owner"],
				"expiration_date" => $fData["expiration_date"],
				"alias" => $fData["alias"],
				"active" => $fData["active"],
				"is_default" => $fData["is_default"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la wallet card a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
