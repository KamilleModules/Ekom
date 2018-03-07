<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\CouponLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$langId = (int)EkomNullosUser::getEkomValue("lang_id");


$idValue = (array_key_exists('coupon_id', $_GET)) ? (int)$_GET['coupon_id'] : null;
$shopIdValue = $shopId;
$isUpdate = (null !== $idValue);


$form = SokoForm::create()
    ->setAction("?" . http_build_query($_GET))
    ->setName("soko-form-shop_coupon");

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
    'fixed' => "Fixed",
    'percent' => "Percent",
    'custom' => "Custom",
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
    ->addControl(SokoInputControl::create()
        ->setName("code")
        ->setLabel('Code')
    )
    ->addControl(SokoChoiceControl::create()
        ->setName("procedure_type")
        ->setLabel('Type')
        ->setChoices($typeChoices)

    )
    ->addControl(SokoInputControl::create()
        ->setName("procedure_operand")
        ->setLabel('Operand')
    )
    ->addControl(SokoInputControl::create()
        ->setName("target")
        ->setLabel('Target')
    )
    ->addControl(SokoBooleanChoiceControl::create()
        ->setValue(1)
        ->setName("active")
        ->setLabel('Active')

    );


$elements = [];
if (true === $isUpdate) {
    $couponId = $idValue;
    $couponCode = CouponLayer::getCouponCodeById($couponId);

    if (array_key_exists("show_form2", $_GET)) {
        $elements[] = [
            'type' => "form",
            'formConfig' => A::getMorphicFormConfig('Ekom', 'shop/shop_coupon_translation', [
                "coupon_id" => $couponId,
                "coupon_code" => $couponCode,
            ]),
        ];
    }

    $elements[] = [
        'type' => "list",
        'listConfig' => A::getMorphicListConfig('Ekom', 'shop/shop_coupon_translation', [
            "coupon_id" => $couponId,
            "coupon_code" => $couponCode,
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
    'title' => "Shop coupon",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'afterFormElements' => $elements,
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue, $idValue) {


        if (null !== $idValue) {
            $q = "select * from ek_coupon 
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
            QuickPdo::update("ek_coupon", [
                "code" => $fData['code'],
                "active" => $fData['active'],
                "procedure_type" => $fData['procedure_type'],
                "procedure_operand" => $fData['procedure_operand'],
                "target" => $fData['target'],
            ], [
                ['id', '=', $idValue],
                ['shop_id', '=', $shopIdValue],
            ]);
            $form->addNotification("Le coupon a bien été mis à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_coupon", [
                    "code" => $fData['code'],
                    "active" => $fData['active'],
                    "procedure_type" => $fData['procedure_type'],
                    "procedure_operand" => $fData['procedure_operand'],
                    "target" => $fData['target'],
                    "shop_id" => $shopIdValue,
                ]);
                $form->addNotification("Le coupon a bien été ajouté", "success");
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