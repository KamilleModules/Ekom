<?php 

use QuickPdo\QuickPdo;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use SokoForm\Form\SokoFormInterface;
use SokoForm\Form\SokoForm;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use Module\Ekom\Utils\E;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\SokoForm\Control\EkomSokoDateControl;




//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'id',
];
$id = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
        
//--------------------------------------------
// UPDATE|INSERT MODE
//--------------------------------------------
$isUpdate = MorphicHelper::getIsUpdate($ric);
        
//--------------------------------------------
// FORM
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Coupon",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_coupon")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("code")
            ->setLabel("Code")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("procedure_type")
            ->setLabel("Procedure_type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("procedure_operand")
            ->setLabel("Procedure_operand")
        )
        ->addControl(SokoInputControl::create()
            ->setName("target")
            ->setLabel("Target")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_coupon"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_coupon", [
				"code" => $fData["code"],
				"active" => $fData["active"],
				"procedure_type" => $fData["procedure_type"],
				"procedure_operand" => $fData["procedure_operand"],
				"target" => $fData["target"],
				"shop_id" => $fData["shop_id"],

            ]);
            $form->addNotification("Le/la Coupon a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_coupon", [
				"code" => $fData["code"],
				"active" => $fData["active"],
				"procedure_type" => $fData["procedure_type"],
				"procedure_operand" => $fData["procedure_operand"],
				"target" => $fData["target"],
				"shop_id" => $fData["shop_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Coupon a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
    //--------------------------------------------
    // IF HAS CONTEXT
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List") . "?coupon_id=$id",
                    "text" => "Voir les cart discounts de ce/cette Coupon",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


