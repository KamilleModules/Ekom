<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\Tag;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$langId = (int)EkomNullosUser::getEkomValue("lang_id");
$id = (array_key_exists('id', $_GET)) ? (int)$_GET['id'] : 0;
$langs = LangLayer::getLangItems();

$langIsReadOnly = ($id !== 0);


$form = SokoForm::create()
    ->setName("soko-form-product_comment")
    ->addControl(SokoInputControl::create()
        ->setName("id")
        ->setLabel('Id')
        ->setProperties([
//                'disabled' => true,
            'readonly' => true,
        ])
        ->setValue($id)
    )
    ->addControl(SokoChoiceControl::create()
        ->setName("lang_id")
        ->setLabel('Lang id')
        ->setChoices($langs)
        ->setProperties([
            'readonly' => $langIsReadOnly,
        ])
        ->setValue($langId)
    )
    //
    ->addControl(SokoInputControl::create()
        ->setName("name")
        ->setLabel('Name')
    )
    ->addValidationRule("name", SokoNotEmptyValidationRule::create());


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Tag",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'feed' => MorphicHelper::getFeedFunction("ek_tag"),
    'process' => function ($fData, SokoFormInterface $form) use ($id) {

        if (0 === $id) {

            Tag::getInst()->create($fData);
            $form->addNotification("Le tag a bien été ajouté", "success");
        } else {
            Tag::getInst()->update($fData, [
                "id" => $id,
            ]);
            $form->addNotification("Le tag a bien été mis à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];