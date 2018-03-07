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

$choice_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from di_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_page_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from di_page", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'group_id',
    'page_id',
];

$group_id = (array_key_exists("group_id", $_GET)) ? $_GET['group_id'] : null;
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
    'title' => "group-page",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_group_has_page")
        ->addControl(SokoChoiceControl::create()
            ->setName("group_id")
            ->setLabel("Group id")
            ->setProperties([
                'readonly' => (null !== $group_id),
            ])
            ->setValue($group_id)
            ->setChoices($choice_group_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("page_id")
            ->setLabel("Page id")
            ->setProperties([
                'readonly' => (null !== $page_id),
            ])
            ->setValue($page_id)
            ->setChoices($choice_page_id))
        ->addControl(SokoInputControl::create()
            ->setName("position")
            ->setLabel("Position")
        ),
    'feed' => MorphicHelper::getFeedFunction("di_group_has_page"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $group_id, $page_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("di_group_has_page", [
				"group_id" => $fData["group_id"],
				"page_id" => $fData["page_id"],
				"position" => $fData["position"],

            ], '', $ric);
            $form->addNotification("Le/la group-page a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("di_group_has_page", [
				"position" => $fData["position"],

            ], [
				["group_id", "=", $group_id],
				["page_id", "=", $page_id],
            
            ]);
            $form->addNotification("Le/la group-page a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
