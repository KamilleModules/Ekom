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

$choice_page_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from di_page", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$page_id = (array_key_exists("page_id", $_GET)) ? $_GET['page_id'] : null;



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
    'title' => "element",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_element")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("page_id")
            ->setLabel("Page id")
            ->setProperties([
                'readonly' => (null !== $page_id),
            ])
            ->setValue($page_id)
            ->setChoices($choice_page_id))
        ->addControl(SokoInputControl::create()
            ->setName("type")
            ->setLabel("Type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("varname")
            ->setLabel("Varname")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pos_x")
            ->setLabel("Pos_x")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pos_y")
            ->setLabel("Pos_y")
        )
        ->addControl(SokoInputControl::create()
            ->setName("width")
            ->setLabel("Width")
        )
        ->addControl(SokoInputControl::create()
            ->setName("height")
            ->setLabel("Height")
        )
        ->addControl(SokoInputControl::create()
            ->setName("validation")
            ->setLabel("Validation")
            ->setType("textarea")
        ),
    'feed' => MorphicHelper::getFeedFunction("di_element"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("di_element", [
				"page_id" => $fData["page_id"],
				"type" => $fData["type"],
				"varname" => $fData["varname"],
				"pos_x" => $fData["pos_x"],
				"pos_y" => $fData["pos_y"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"validation" => $fData["validation"],

            ], '', $ric);
            $form->addNotification("Le/la element a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("di_element", [
				"page_id" => $fData["page_id"],
				"type" => $fData["type"],
				"varname" => $fData["varname"],
				"pos_x" => $fData["pos_x"],
				"pos_y" => $fData["pos_y"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"validation" => $fData["validation"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la element a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_DiUserHasElement_List") . "?s&element_id=$id",
                    "text" => "Voir les user-elements",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
