<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\Exception\QuickPdoException;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$idValue = (array_key_exists('id', $_GET)) ? (int)$_GET['id'] : null;
//$shopIdValue = (array_key_exists('shop_id', $_GET)) ? $_GET['shop_id'] : $shopId;
$shopIdValue = $shopId;
$isUpdate = (null !== $idValue);


$form = SokoForm::create()
    ->setAction("?" . http_build_query($_GET))
    ->setName("soko-form-shop_provider");

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


//--------------------------------------------
// CONF
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Shop Provider",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'feed' => MorphicHelper::getFeedFunctionByQuery("select * from ek_provider where id=$idValue"),
    'process' => function ($fData, SokoFormInterface $form) use ($shopIdValue, $isUpdate, $idValue) {

        if (true === $isUpdate) { // update
            QuickPdo::update("ek_provider", [
                "name" => $fData['name'],
            ], [
                ['id', '=', $idValue],
            ]);
            $form->addNotification("Le fournisseur a bien été mis à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_provider", [
                    "shop_id" => $shopIdValue,
                    "name" => $fData['name'],
                ]);
                $form->addNotification("Le fournisseur a bien été ajouté", "success");
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