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
    'title' => "uploaded",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_uploaded")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoInputControl::create()
            ->setName("path")
            ->setLabel("Path")
            ->setType("textarea")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_upload")
            ->setLabel("Date_upload")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("ip")
            ->setLabel("Ip")
        )
        ->addControl(SokoInputControl::create()
            ->setName("http_user_agent")
            ->setLabel("Http_user_agent")
        ),
    'feed' => MorphicHelper::getFeedFunction("di_uploaded"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("di_uploaded", [
				"path" => $fData["path"],
				"content" => $fData["content"],
				"date_upload" => $fData["date_upload"],
				"ip" => $fData["ip"],
				"http_user_agent" => $fData["http_user_agent"],

            ], '', $ric);
            $form->addNotification("Le/la uploaded a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("di_uploaded", [
				"path" => $fData["path"],
				"content" => $fData["content"],
				"date_upload" => $fData["date_upload"],
				"ip" => $fData["ip"],
				"http_user_agent" => $fData["http_user_agent"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la uploaded a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
