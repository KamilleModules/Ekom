<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$langId = (int)EkomNullosUser::getEkomValue("lang_id");


$idValue = (array_key_exists('discount_id', $_GET)) ? (int)$_GET['discount_id'] : null;
$shopIdValue = $shopId;
$isUpdate = (null !== $idValue);




$form = SokoForm::create()
    ->setAction("?" . http_build_query($_GET))
    ->setName("soko-form-shop_discount");

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


$typeChoices = [
    'amount' => "Amount",
    'percent' => "Percent",
];

$targetChoices = [
    'priceWithTax' => "priceWithTax",
    'priceWithoutTax' => "priceWithoutTax",
];


$form
    ->addControl(SokoInputControl::create()
        ->setName("shop_id")
        ->setLabel('Shop id')
        ->setProperties([
            'readonly' => true,
        ])
        ->setValue($shopIdValue)
    )
    ->addControl(SokoChoiceControl::create()
        ->setName("type")
        ->setLabel('Type')
        ->setChoices($typeChoices)

    )
    ->addControl(SokoChoiceControl::create()
        ->setName("target")
        ->setLabel('Target')
        ->setChoices($targetChoices)

    )
    ->addControl(SokoInputControl::create()
        ->setName("operand")
        ->setLabel('Valeur')
    );


$elements = [];
if (true === $isUpdate) {
    $discountId = $idValue;
//    $discountLabel = CategoryLayer::getNameById($discountId);
    $discountLabel = DiscountLayer::getHumanIdentifier($discountId);

    if (array_key_exists("show_form2", $_GET)) {
        $elements[] = [
            'type' => "form",
            'formConfig' => A::getMorphicFormConfig('Ekom', 'shop/shop_discount_translation', [
                "discount_id" => $discountId,
                "discount_label" => $discountLabel,
            ]),
        ];
    }

    $elements[] = [
        'type' => "list",
        'listConfig' => A::getMorphicListConfig('Ekom', 'shop/shop_discount_translation', [
            "discount_id" => $discountId,
            "discount_label" => $discountLabel,
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
    'title' => "Shop discount",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'afterFormElements' => $elements,
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue, $idValue) {


        if (null !== $idValue) {
            $q = "select * from ek_discount 
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
            QuickPdo::update("ek_discount", [
                "type" => $fData['type'],
                "operand" => $fData['operand'],
                "target" => $fData['target'],
            ], [
                ['id', '=', $idValue],
                ['shop_id', '=', $shopIdValue],
            ]);
            $form->addNotification("La réduction de produit a bien été mise à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_discount", [
                    "type" => $fData['type'],
                    "operand" => $fData['operand'],
                    "target" => $fData['target'],
                    "shop_id" => $shopIdValue,
                ]);
                $form->addNotification("La réduction de produit a bien été ajoutée", "success");
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