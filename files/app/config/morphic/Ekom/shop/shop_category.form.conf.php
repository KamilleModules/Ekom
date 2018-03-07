<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$langId = (int)EkomNullosUser::getEkomValue("lang_id");


$idValue = (array_key_exists('category_id', $_GET)) ? (int)$_GET['category_id'] : null;
$shopIdValue = $shopId;
$isUpdate = (null !== $idValue);


$nbCategories = CategoryLayer::countCategoriesByShopId($shopId);
$catControl = null;

if ($nbCategories > 200) {
    $catControl = SokoAutocompleteInputControl::create()
        ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
            'action' => "auto.category",
        ]))
        ->setName("category_id")
        ->setLabel('Category id');


} else {
    $categories = CategoryLayer::getCategoryItemsByShopId($shopId);
    $catControl = SokoChoiceControl::create()
        ->setName("category_id")
        ->setLabel('Category Id')
        ->setChoices($categories);
}


$form = SokoForm::create()
    ->setAction("?" . http_build_query($_GET))
    ->setName("soko-form-shop_category");

if (true === $isUpdate) {
    $form
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel('Id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shopIdValue)
        );
}
$form
    ->addControl(SokoInputControl::create()
        ->setName("shop_id")
        ->setLabel('Shop id')
        ->setProperties([
            'readonly' => true,
        ])
        ->setValue($shopIdValue)
    )
    ->addControl(SokoInputControl::create()
        ->setName("name")
        ->setLabel('Name')
    )
    ->addControl($catControl)
    ->addControl(SokoInputControl::create()
        ->setName("order")
        ->setLabel('Order')
    );


$elements = [];
if (true === $isUpdate) {
    $categoryId = $idValue;
    $categoryName = CategoryLayer::getNameById($categoryId);

    if (array_key_exists("show_form2", $_GET)) {
        $elements[] = [
            'type' => "form",
            'formConfig' => A::getMorphicFormConfig('Ekom', 'shop/shop_category_translation', [
                "category_id" => $categoryId,
                "category_name" => $categoryName,
            ]),
        ];
    }

    $elements[] = [
        'type' => "list",
        'listConfig' => A::getMorphicListConfig('Ekom', 'shop/shop_category_translation', [
            "category_id" => $categoryId,
            "category_name" => $categoryName,
        ]),
    ];
}


//--------------------------------------------
// CONF
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Shop category",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'afterFormElements' => $elements,
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue, $idValue) {


        if (null !== $idValue) {
            $q = "select * from ek_category 
where id=$idValue 
and shop_id=$shopIdValue

";
            $row = QuickPdo::fetch($q);
            if (false !== $row) {
                $form->inject($row);
            }
        }
    },
    'process' => function ($fData, SokoFormInterface $form) use ($shopIdValue, $isUpdate, $idValue) {

        if (true === $isUpdate) { // update
            QuickPdo::update("ek_category", [
                "name" => $fData['name'],
                "order" => $fData['order'],
                "category_id" => $fData['category_id'],
            ], [
                ['id', '=', $idValue],
                ['shop_id', '=', $shopIdValue],
            ]);
            $form->addNotification("La catégorie a bien été mise à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_category", [
                    "name" => $fData['name'],
                    "category_id" => $fData['category_id'],
                    "shop_id" => $shopIdValue,
                    "order" => $fData['order'],
                ]);
                $form->addNotification("La catégorie a bien été ajoutée", "success");
            } catch (\PDOException $e) {
                if (QuickPdoExceptionTool::isDuplicateEntry($e)) {
                    $form->addNotification("Cette entrée existe déjà", "warning");
                } else {
                    throw $e;
                }
            }
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];