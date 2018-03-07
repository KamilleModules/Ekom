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

$choice_provider_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_provider", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_has_product_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_has_product_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'provider_id',
    'shop_has_product_shop_id',
    'shop_has_product_product_id',
];

$provider_id = (array_key_exists("provider_id", $_GET)) ? $_GET['provider_id'] : null;
$shop_has_product_shop_id = (array_key_exists("shop_has_product_shop_id", $_GET)) ? $_GET['shop_has_product_shop_id'] : null;
$shop_has_product_product_id = (array_key_exists("shop_has_product_product_id", $_GET)) ? $_GET['shop_has_product_product_id'] : null;



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
    'title' => "provider-shop-product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_provider_has_shop_has_product")
        ->addControl(SokoChoiceControl::create()
            ->setName("provider_id")
            ->setLabel("Provider id")
            ->setProperties([
                'readonly' => (null !== $provider_id),
            ])
            ->setValue($provider_id)
            ->setChoices($choice_provider_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_has_product_shop_id")
            ->setLabel("Shop has product shop id")
            ->setProperties([
                'readonly' => (null !== $shop_has_product_shop_id),
            ])
            ->setValue($shop_has_product_shop_id)
            ->setChoices($choice_shop_has_product_shop_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_has_product_product_id")
            ->setLabel("Shop has product product id")
            ->setProperties([
                'readonly' => (null !== $shop_has_product_product_id),
            ])
            ->setValue($shop_has_product_product_id)
            ->setChoices($choice_shop_has_product_product_id)),
    'feed' => MorphicHelper::getFeedFunction("ek_provider_has_shop_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $provider_id, $shop_has_product_shop_id, $shop_has_product_product_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_provider_has_shop_has_product", [
				"provider_id" => $fData["provider_id"],
				"shop_has_product_shop_id" => $fData["shop_has_product_shop_id"],
				"shop_has_product_product_id" => $fData["shop_has_product_product_id"],

            ], '', $ric);
            $form->addNotification("Le/la provider-shop-product a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_provider_has_shop_has_product", [

            ], [
				["provider_id", "=", $provider_id],
				["shop_has_product_shop_id", "=", $shop_has_product_shop_id],
				["shop_has_product_product_id", "=", $shop_has_product_product_id],
            
            ]);
            $form->addNotification("Le/la provider-shop-product a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
