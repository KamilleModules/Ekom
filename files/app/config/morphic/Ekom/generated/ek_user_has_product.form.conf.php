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
$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from ek_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$product_id = (array_key_exists("product_id", $_GET)) ? $_GET['product_id'] : null;
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
    'title' => "user-product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_user_has_product")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => (null !== $user_id),
            ])
            ->setValue($user_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.user",
            ]))         )
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))         )
        ->addControl(SokoInputControl::create()
            ->setName("product_details")
            ->setLabel("Product_details")
            ->setType("textarea")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date")
            ->setLabel("Date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("deleted_date")
            ->setLabel("Deleted_date")
            ->addProperties([
                "required" => false,                       
            ])
                        
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_user_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_user_has_product", [
				"user_id" => $fData["user_id"],
				"product_id" => $fData["product_id"],
				"product_details" => $fData["product_details"],
				"date" => $fData["date"],
				"deleted_date" => $fData["deleted_date"],

            ], '', $ric);
            $form->addNotification("Le/la user-product a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_user_has_product", [
				"user_id" => $fData["user_id"],
				"product_id" => $fData["product_id"],
				"product_details" => $fData["product_details"],
				"date" => $fData["date"],
				"deleted_date" => $fData["deleted_date"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la user-product a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
