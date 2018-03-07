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
    'title' => "user tracker",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-eut_user_tracker")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User_id")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date")
            ->setLabel("Date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("host")
            ->setLabel("Host")
        )
        ->addControl(SokoInputControl::create()
            ->setName("route")
            ->setLabel("Route")
        )
        ->addControl(SokoInputControl::create()
            ->setName("ip")
            ->setLabel("Ip")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("https")
            ->setLabel("Https")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("http_referer")
            ->setLabel("Http_referer")
        )
        ->addControl(SokoInputControl::create()
            ->setName("uri")
            ->setLabel("Uri")
        )
        ->addControl(SokoInputControl::create()
            ->setName("get")
            ->setLabel("Get")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("post")
            ->setLabel("Post")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("http_user_agent")
            ->setLabel("Http_user_agent")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("http_accept_language")
            ->setLabel("Http_accept_language")
            ->setType("textarea")
        ),
    'feed' => MorphicHelper::getFeedFunction("eut_user_tracker"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("eut_user_tracker", [
				"user_id" => $fData["user_id"],
				"date" => $fData["date"],
				"host" => $fData["host"],
				"route" => $fData["route"],
				"ip" => $fData["ip"],
				"https" => $fData["https"],
				"http_referer" => $fData["http_referer"],
				"uri" => $fData["uri"],
				"get" => $fData["get"],
				"post" => $fData["post"],
				"http_user_agent" => $fData["http_user_agent"],
				"http_accept_language" => $fData["http_accept_language"],

            ], '', $ric);
            $form->addNotification("Le/la user tracker a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("eut_user_tracker", [
				"user_id" => $fData["user_id"],
				"date" => $fData["date"],
				"host" => $fData["host"],
				"route" => $fData["route"],
				"ip" => $fData["ip"],
				"https" => $fData["https"],
				"http_referer" => $fData["http_referer"],
				"uri" => $fData["uri"],
				"get" => $fData["get"],
				"post" => $fData["post"],
				"http_user_agent" => $fData["http_user_agent"],
				"http_accept_language" => $fData["http_accept_language"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la user tracker a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
