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
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;



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
    'title' => "page",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_page")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoInputControl::create()
            ->setName("name")
            ->setLabel("Name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("bg_document")
            ->setLabel("Bg_document")
        )
        ->addControl(SokoInputControl::create()
            ->setName("thumb")
            ->setLabel("Thumb")
        )
        ->addControl(SokoInputControl::create()
            ->setName("width")
            ->setLabel("Width")
        )
        ->addControl(SokoInputControl::create()
            ->setName("height")
            ->setLabel("Height")
        ),
    'feed' => MorphicHelper::getFeedFunction("di_page"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("di_page", [
				"name" => $fData["name"],
				"bg_document" => $fData["bg_document"],
				"thumb" => $fData["thumb"],
				"width" => $fData["width"],
				"height" => $fData["height"],

            ], '', $ric);
            $form->addNotification("Le/la page a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("di_page", [
				"name" => $fData["name"],
				"bg_document" => $fData["bg_document"],
				"thumb" => $fData["thumb"],
				"width" => $fData["width"],
				"height" => $fData["height"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la page a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,            
    //--------------------------------------------
    // CHILDREN
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_DiElement_List") . "?s&page_id=$id",
                    "text" => "Voir les elements",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_DiGroupHasPage_List") . "?s&page_id=$id",
                    "text" => "Voir les group-pages",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
