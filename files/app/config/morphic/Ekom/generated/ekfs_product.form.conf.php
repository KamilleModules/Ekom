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

$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_lang", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$lang_id = (array_key_exists("lang_id", $_GET)) ? $_GET['lang_id'] : $lang_id; // inferred
$product_id = (array_key_exists("product_id", $_GET)) ? $_GET['product_id'] : null;
$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // inferred



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
    'title' => "product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekfs_product")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel("Lang id")
            ->setProperties([
                'readonly' => (null !== $lang_id),
            ])
            ->setValue($lang_id)
            ->setChoices($choice_lang_id))
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel("Label")
        )
        ->addControl(SokoInputControl::create()
            ->setName("ref")
            ->setLabel("Ref")
        )
        ->addControl(SokoInputControl::create()
            ->setName("sale_price_without_tax")
            ->setLabel("Sale_price_without_tax")
        )
        ->addControl(SokoInputControl::create()
            ->setName("sale_price_with_tax")
            ->setLabel("Sale_price_with_tax")
        )
        ->addControl(SokoInputControl::create()
            ->setName("attr_string")
            ->setLabel("Attr_string")
        )
        ->addControl(SokoInputControl::create()
            ->setName("uri_card")
            ->setLabel("Uri_card")
        )
        ->addControl(SokoInputControl::create()
            ->setName("uri_thumb")
            ->setLabel("Uri_thumb")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
            ->setChoices($choice_product_id)),
    'feed' => MorphicHelper::getFeedFunction("ekfs_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ekfs_product", [
				"shop_id" => $fData["shop_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],
				"ref" => $fData["ref"],
				"sale_price_without_tax" => $fData["sale_price_without_tax"],
				"sale_price_with_tax" => $fData["sale_price_with_tax"],
				"attr_string" => $fData["attr_string"],
				"uri_card" => $fData["uri_card"],
				"uri_thumb" => $fData["uri_thumb"],
				"product_id" => $fData["product_id"],

            ], '', $ric);
            $form->addNotification("Le/la product a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ekfs_product", [
				"shop_id" => $fData["shop_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],
				"ref" => $fData["ref"],
				"sale_price_without_tax" => $fData["sale_price_without_tax"],
				"sale_price_with_tax" => $fData["sale_price_with_tax"],
				"attr_string" => $fData["attr_string"],
				"uri_card" => $fData["uri_card"],
				"uri_thumb" => $fData["uri_thumb"],
				"product_id" => $fData["product_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la product a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
