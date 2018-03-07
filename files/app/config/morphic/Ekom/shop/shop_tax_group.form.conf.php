<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$idValue = (array_key_exists('tax_group_id', $_GET)) ? (int)$_GET['tax_group_id'] : null;
$shopIdValue = $shopId;
$isUpdate = (null !== $idValue);


$form = SokoForm::create()
    ->setAction("?" . http_build_query($_GET))
    ->setName("soko-form-shop_tax_group");

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
    ->addControl(SokoInputControl::create()
        ->setName("label")
        ->setLabel('Label')
    )
;


$elements = [];
if (true === $isUpdate) {
    $taxGroupId = $idValue;
    $taxGroupLabel = TaxLayer::getGroupLabelByGroupId($taxGroupId);

    if (array_key_exists("show_form2", $_GET)) {
        $elements[] = [
            'type' => "form",
            'formConfig' => A::getMorphicFormConfig('Ekom', 'shop/shop_tax_group_tax', [
                "tax_group_id" => $taxGroupId,
                "tax_group_label" => $taxGroupLabel,
            ]),
        ];
    }

    $elements[] = [
        'type' => "list",
        'listConfig' => A::getMorphicListConfig('Ekom', 'shop/shop_tax_group_tax', [
            "tax_group_id" => $taxGroupId,
            "tax_group_label" => $taxGroupLabel,
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
    'title' => "Shop tax group",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'afterFormElements' => $elements,
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue, $idValue) {


        if (null !== $idValue) {
            $q = "select * from ek_tax_group 
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
            QuickPdo::update("ek_tax_group", [
                "name" => $fData['name'],
                "label" => $fData['label'],
            ], [
                ['id', '=', $idValue],
            ]);
            $form->addNotification("Le groupe de taxe a bien été mis à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_tax_group", [
                    "shop_id" => $shopIdValue,
                    "name" => $fData['name'],
                    "label" => $fData['label'],
                ]);
                $form->addNotification("Le groupe de taxe a bien été ajouté", "success");
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