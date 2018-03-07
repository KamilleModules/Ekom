<?php


use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\DiscountLang;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

if (
    array_key_exists("discount_id", $context) &&
    array_key_exists("discount_label", $context)
) {

    $discount_id = $context['discount_id'];
    $discount_label = $context['discount_label'];
    $contextLangId = (int)EkomNullosUser::getEkomValue("lang_id");

    $langId = (array_key_exists("lang_id", $_GET)) ? (int)$_GET['lang_id'] : 0;


    $isUpdate = (0 !== $langId);
    if (0 === $langId) {
        $langId = $contextLangId;
    }


    //--------------------------------------------
    // HYBRID CURRENCY CONTROL
    //--------------------------------------------
    $langItems = LangLayer::getLangItems();
    $langControl = SokoChoiceControl::create()
        ->setChoices($langItems)
        ->setName("lang_id")
        ->setLabel('Lang id')
        ->setValue($langId);


    if ($isUpdate) {
        $langControl->setProperties([
            'readonly' => true,
        ]);
    }


    //--------------------------------------------
    // CONF
    //--------------------------------------------
    $conf = [
        //--------------------------------------------
        // FORM WIDGET
        //--------------------------------------------
        'title' => "Shop discount \"" . $discount_label . "\" translation",
        //--------------------------------------------
        // SOKO FORM
        'form' => SokoForm::create()
            ->setAction("?" . http_build_query($_GET))
            ->setName("soko-form-shop_discount_lang")
            ->addControl(SokoInputControl::create()
                ->setName("discount_id")
                ->setLabel('Discount id')
                ->setProperties([
                    'readonly' => true,
                ])
                ->setValue($discount_id)
            )
            ->addControl($langControl)
            ->addControl(SokoInputControl::create()
                ->setName("label")
                ->setLabel('Label')
            )
        ,
        'feed' => function (SokoFormInterface $form, array $ric) use ($discount_id, $langId) {
            if (null !== $discount_id) {
                $markers = [];

                $discount_id = (int)$discount_id;
                $q = "select *
from ek_discount_lang 
where discount_id=$discount_id
and lang_id=$langId
";
                $row = QuickPdo::fetch("$q", $markers);
            } else {
                $row = ['discount_id' => $discount_id];
            }
            $form->inject($row);
        },
        'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $discount_id, $langId) {

            if (true === $isUpdate) { // update
                DiscountLang::getInst()->update($fData, [
                    "discount_id" => $fData['discount_id'],
                    "lang_id" => $fData['lang_id'],
                ]);

                $form->addNotification("La traduction de réduction de produit a bien été mise à jour", "success");
            } else {
                try {
                    DiscountLang::getInst()->create($fData);
                    $form->addNotification("La traduction de réduction de produit a bien été ajoutée", "success");
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
            'discount_id',
            'lang_id',
        ],
    ];


} else {
    throw new EkomException("Some variables not found in the given context");
}