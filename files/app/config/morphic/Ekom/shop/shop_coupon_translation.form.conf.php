<?php


use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\CouponLang;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

if (
    array_key_exists("coupon_id", $context) &&
    array_key_exists("coupon_code", $context)
) {

    $coupon_id = $context['coupon_id'];
    $coupon_code = $context['coupon_code'];
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
        'title' => "Shop coupon \"" . $coupon_code . "\" translation",
        //--------------------------------------------
        // SOKO FORM
        'form' => SokoForm::create()
            ->setAction("?" . http_build_query($_GET))
            ->setName("soko-form-shop_coupon_lang")
            ->addControl(SokoInputControl::create()
                ->setName("coupon_id")
                ->setLabel('Coupon id')
                ->setProperties([
                    'readonly' => true,
                ])
                ->setValue($coupon_id)
            )
            ->addControl($langControl)
            ->addControl(SokoInputControl::create()
                ->setName("label")
                ->setLabel('Label')
            )
        ,
        'feed' => function (SokoFormInterface $form, array $ric) use ($coupon_id, $langId) {
            if (null !== $coupon_id) {
                $markers = [];

                $coupon_id = (int)$coupon_id;
                $q = "select *
from ek_coupon_lang 
where coupon_id=$coupon_id
and lang_id=$langId
";
                $row = QuickPdo::fetch("$q", $markers);
            } else {
                $row = ['coupon_id' => $coupon_id];
            }
            $form->inject($row);
        },
        'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $coupon_id, $langId) {

            if (true === $isUpdate) { // update
                CouponLang::getInst()->update($fData, [
                    "coupon_id" => $fData['coupon_id'],
                    "lang_id" => $fData['lang_id'],
                ]);

                $form->addNotification("La traduction de coupon a bien été mise à jour", "success");
            } else {
                try {
                    CouponLang::getInst()->create($fData);
                    $form->addNotification("La traduction de coupon a bien été ajoutée", "success");
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
            'coupon_id',
            'lang_id',
        ],
    ];


} else {
    throw new EkomException("Some variables not found in the given context");
}