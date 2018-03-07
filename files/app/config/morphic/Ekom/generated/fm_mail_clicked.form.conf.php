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

$choice_mail_link_id = QuickPdo::fetchAll("select id, concat(id, \". \", link_name) as label from fm_mail_link", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$mail_link_id = (array_key_exists("mail_link_id", $_GET)) ? $_GET['mail_link_id'] : null;



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
    'title' => "mail clicked",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-fm_mail_clicked")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("mail_link_id")
            ->setLabel("Mail link id")
            ->setProperties([
                'readonly' => (null !== $mail_link_id),
            ])
            ->setValue($mail_link_id)
            ->setChoices($choice_mail_link_id))
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_clicked")
            ->setLabel("Date_clicked")
            ->addProperties([
                "required" => true,                       
            ])
                        
        ),
    'feed' => MorphicHelper::getFeedFunction("fm_mail_clicked"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("fm_mail_clicked", [
				"mail_link_id" => $fData["mail_link_id"],
				"date_clicked" => $fData["date_clicked"],

            ], '', $ric);
            $form->addNotification("Le/la mail clicked a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("fm_mail_clicked", [
				"mail_link_id" => $fData["mail_link_id"],
				"date_clicked" => $fData["date_clicked"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la mail clicked a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
