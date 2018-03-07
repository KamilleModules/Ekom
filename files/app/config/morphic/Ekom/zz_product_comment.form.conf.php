<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Object\ProductComment;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$id = (array_key_exists('id', $_GET)) ? (int)$_GET['id'] : 0;

$ratingChoices = range(0, 100);


$dateControl = SokoInputControl::create()
    ->setName("date")
    ->setLabel('Date');
if (0 !== $id) {
    $dateControl->setProperties([
        "readonly" => true,
    ]);
}
else{
    $dateControl->setProperties([
        "disabled" => true,
    ]);
}


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
    ->addControl(SokoInputControl::create()
        ->setName("shop_id")
        ->setLabel('Shop id')
        ->setProperties([
            'readonly' => true,
        ])
        ->setValue($shopId)
    )
    //
    ->addControl(SokoAutocompleteInputControl::create()
        ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
            'action' => "auto.product",
        ]))
        ->setName("product_id")
        ->setLabel('Product id'))
    //
    ->addControl(SokoAutocompleteInputControl::create()
        ->setName("user_id")
        ->setLabel('User id')
        ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
            'action' => "auto.user",
        ]))
    )
    ->addControl($dateControl)
    ->addControl(SokoChoiceControl::create()
        ->setName("rating")
        ->setLabel('Rating')
        ->setChoices($ratingChoices)
        ->setProperties([
            'required' => true,
        ])
    )
    ->addControl(SokoInputControl::create()
        ->setName("title")
        ->setLabel('Title')
    )
    ->addControl(SokoInputControl::create()
        ->setName("comment")
        ->setLabel('Comment')
        ->setType("textarea")
    )
    ->addControl(SokoBooleanChoiceControl::create()
        ->setName("active")
        ->setLabel('Active')
        ->setValue(1)
    )
    ->addValidationRule("product_id", SokoNotEmptyValidationRule::create())
    ->addValidationRule("user_id", SokoNotEmptyValidationRule::create())
    ->addValidationRule("comment", SokoNotEmptyValidationRule::create());


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product comment",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'feed' => MorphicHelper::getFeedFunction("ek_product_comment"),
    'process' => function ($fData, SokoFormInterface $form) use ($shopId, $id) {

        if (0 === $id) {
            $fData['date'] = date("Y-m-d H:i:s");
            ProductComment::getInst()->create($fData);
            $form->addNotification("Le commentaire a bien été ajouté", "success");
        } else {
            ProductComment::getInst()->update($fData, [
                "id" => $id,
            ]);
            $form->addNotification("Le commentaire a bien été mis à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];