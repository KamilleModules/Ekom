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


$choice_shop_has_product_shop_id = QuickPdo::fetchAll("select shop_id, concat(shop_id, '. ', _discount_badge) as label from kamille.ek_shop_has_product", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_shop_has_product_product_id = QuickPdo::fetchAll("select product_id, concat(product_id, '. ', _discount_badge) as label from kamille.ek_shop_has_product", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$provider_id = MorphicHelper::getFormContextValue("provider_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$shop_has_product_shop_id = (array_key_exists('shop_has_product_shop_id', $_GET)) ? $_GET['shop_has_product_shop_id'] : null;
$shop_has_product_product_id = (array_key_exists('shop_has_product_product_id', $_GET)) ? $_GET['shop_has_product_product_id'] : null;
        
//--------------------------------------------
// UPDATE|INSERT MODE
//--------------------------------------------
$isUpdate = (false === array_key_exists("form", $_GET));
        
//--------------------------------------------
// FORM
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Provider has shop has product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-provider_has_shop_has_product")
        ->addControl(SokoInputControl::create()
            ->setName("provider_id")
            ->setLabel("Provider id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($provider_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_has_product_shop_id")
            ->setLabel('Shop has product shop id')
            ->setChoices($choice_shop_has_product_shop_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($shop_has_product_shop_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_has_product_product_id")
            ->setLabel('Shop has product product id')
            ->setChoices($choice_shop_has_product_product_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($shop_has_product_product_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_provider_has_shop_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $provider_id, $shop_has_product_shop_id, $shop_has_product_product_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_provider_has_shop_has_product", [
				"provider_id" => $fData["provider_id"],
				"shop_has_product_shop_id" => $fData["shop_has_product_shop_id"],
				"shop_has_product_product_id" => $fData["shop_has_product_product_id"],

            ]);
            $form->addNotification("Le/la Provider has shop has product pour le/la provider \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_provider_has_shop_has_product", [

            ], [
				["provider_id", "=", $provider_id],
				["shop_has_product_shop_id", "=", $shop_has_product_shop_id],
				["shop_has_product_product_id", "=", $shop_has_product_product_id],
            
            ]);
            $form->addNotification("Le/la Provider has shop has product pour le/la provider \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'provider_id',
        'shop_has_product_shop_id',
        'shop_has_product_product_id',
    ],
];


