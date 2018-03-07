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




$ric = [
    'category_id',
];

$category_id = (array_key_exists("category_id", $_GET)) ? $_GET['category_id'] : null;



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
    'title' => "nested category",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-nested_category")
        ->addControl(SokoInputControl::create()
            ->setName("category_id")
            ->setLabel("Category id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($category_id))
        ->addControl(SokoInputControl::create()
            ->setName("name")
            ->setLabel("Name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("lft")
            ->setLabel("Lft")
        )
        ->addControl(SokoInputControl::create()
            ->setName("rgt")
            ->setLabel("Rgt")
        ),
    'feed' => MorphicHelper::getFeedFunction("nested_category"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $category_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("nested_category", [
				"name" => $fData["name"],
				"lft" => $fData["lft"],
				"rgt" => $fData["rgt"],

            ], '', $ric);
            $form->addNotification("Le/la nested category a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("nested_category", [
				"name" => $fData["name"],
				"lft" => $fData["lft"],
				"rgt" => $fData["rgt"],

            ], [
				["category_id", "=", $category_id],
            
            ]);
            $form->addNotification("Le/la nested category a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
