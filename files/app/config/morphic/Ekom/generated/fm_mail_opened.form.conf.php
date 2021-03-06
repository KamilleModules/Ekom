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

$choice_mail_id = QuickPdo::fetchAll("select id, concat(id, \". \", type) as label from fm_mail", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$mail_id = (array_key_exists("mail_id", $_GET)) ? $_GET['mail_id'] : null;



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
    'title' => "mail opened",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-fm_mail_opened")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("mail_id")
            ->setLabel("Mail id")
            ->setProperties([
                'readonly' => (null !== $mail_id),
            ])
            ->setValue($mail_id)
            ->setChoices($choice_mail_id))
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_opened")
            ->setLabel("Date_opened")
            ->addProperties([
                "required" => true,                       
            ])
                        
        ),
    'feed' => MorphicHelper::getFeedFunction("fm_mail_opened"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("fm_mail_opened", [
				"mail_id" => $fData["mail_id"],
				"date_opened" => $fData["date_opened"],

            ], '', $ric);
            $form->addNotification("Le/la mail opened a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("fm_mail_opened", [
				"mail_id" => $fData["mail_id"],
				"date_opened" => $fData["date_opened"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la mail opened a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
