<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$idValue = (array_key_exists('seller_id', $_GET)) ? (int)$_GET['seller_id'] : null;
$shopIdValue = $shopId;
$isUpdate = (null !== $idValue);


$form = SokoForm::create()
    ->setAction("?" . http_build_query($_GET))
    ->setName("soko-form-shop_seller");

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
    );


$elements = [];
if (true === $isUpdate) {
    $sellerId = $idValue;
    $sellerName = SellerLayer::getNameById($sellerId);

//    if (array_key_exists("show_form2", $_GET)) {
//        $elements[] = [
//            'type' => "form",
//            'formConfig' => A::getMorphicFormConfig('Ekom', 'shop/shop_seller_address', [
//                "seller_id" => $sellerId,
//                "seller_name" => $sellerName,
//            ]),
//        ];
//    }

    $elements[] = [
        'type' => "list",
        'listConfig' => A::getMorphicListConfig('Ekom', 'shop/shop_seller_address', [
            "seller_id" => $sellerId,
            "seller_name" => $sellerName,
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
    'title' => "Shop seller",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'afterFormElements' => $elements,
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue, $idValue) {


        if (null !== $idValue) {
            $q = "select * from ek_seller 
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
            QuickPdo::update("ek_seller", [
                "name" => $fData['name'],
            ], [
                ['id', '=', $idValue],
            ]);
            $form->addNotification("Le vendeur a bien été mis à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_seller", [
                    "shop_id" => $shopIdValue,
                    "name" => $fData['name'],
                ]);
                $form->addNotification("Le vendeur a bien été ajouté", "success");
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