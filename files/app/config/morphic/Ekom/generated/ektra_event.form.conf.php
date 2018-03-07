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

$choice_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_location_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ektra_location", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_date_range_id = QuickPdo::fetchAll("select id, concat(id, \". \", shop_id) as label from ektra_date_range", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_trainer_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ektra_trainer_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$product_id = (array_key_exists("product_id", $_GET)) ? $_GET['product_id'] : null;
$location_id = (array_key_exists("location_id", $_GET)) ? $_GET['location_id'] : null;
$date_range_id = (array_key_exists("date_range_id", $_GET)) ? $_GET['date_range_id'] : null;
$trainer_group_id = (array_key_exists("trainer_group_id", $_GET)) ? $_GET['trainer_group_id'] : null;



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
    'title' => "event",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_event")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
            ->setChoices($choice_product_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("location_id")
            ->setLabel("Location id")
            ->setProperties([
                'readonly' => (null !== $location_id),
            ])
            ->setValue($location_id)
            ->setChoices($choice_location_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("date_range_id")
            ->setLabel("Date range id")
            ->setProperties([
                'readonly' => (null !== $date_range_id),
            ])
            ->setValue($date_range_id)
            ->setChoices($choice_date_range_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("trainer_group_id")
            ->setLabel("Trainer group id")
            ->setProperties([
                'readonly' => (null !== $trainer_group_id),
            ])
            ->setValue($trainer_group_id)
            ->setChoices($choice_trainer_group_id))
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop_id")
        ),
    'feed' => MorphicHelper::getFeedFunction("ektra_event"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ektra_event", [
				"product_id" => $fData["product_id"],
				"location_id" => $fData["location_id"],
				"date_range_id" => $fData["date_range_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"shop_id" => $fData["shop_id"],

            ], '', $ric);
            $form->addNotification("Le/la event a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ektra_event", [
				"product_id" => $fData["product_id"],
				"location_id" => $fData["location_id"],
				"date_range_id" => $fData["date_range_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"shop_id" => $fData["shop_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la event a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
