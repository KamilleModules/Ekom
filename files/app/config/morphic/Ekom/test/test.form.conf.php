<?php


use Kamille\Services\XConfig;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Object\Seller;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use SafeUploader\Tool\SafeUploaderHelperTool;
use SokoForm\Control\SokoInputControl;
use SokoForm\Control\SokoSafeUploadControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$ric = [
    "id",
];


//--------------------------------------------
// foreach ric
//--------------------------------------------
$id = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
// endforeach ric


// it's an update if all ric are present in the uri
$isUpdate = MorphicHelper::getIsUpdate($ric);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Seller",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-seller")
        //--------------------------------------------
        // foreach ric
        //--------------------------------------------
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel('Id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
        // end foreach
        ->addControl(SokoInputControl::create()
            ->setName("name")
            ->setLabel('Name')
            ->setProperties([
                'required' => false,
            ])
        )
        ->addControl(SokoSafeUploadControl::create()
            ->setName("image")
            ->setLabel('Image')
            ->setProfileId("test.image")
            ->setRic($ric)
            ->setProperties([
                'required' => false,
            ])
        )
        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_seller"),

    /**
     * In use, pass the following:
     * - $isUpdate
     * - all rics
     */
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");


        if (false === $isUpdate) {
//            a($_POST);


            $ric = QuickPdo::insert("ek_seller", [
                "name" => $fData['name'],
                "shop_id" => $fData['shop_id'],
            ], '', true);


            $profileId = "test.image";
            SafeUploaderHelperTool::fixTemporaryPaths($profileId, implode('-', $ric));
            $form->addNotification("Le vendeur a bien été ajouté", "success");
        } else {


            Seller::getInst()->update($fData, [
                "id" => $id,
            ]);
            $form->addNotification("Le vendeur a bien été mis à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
    //--------------------------------------------
    // referenced keys
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",

            //--------------------------------------------
            // foreach referenced keys
            // a(QuickPdoInfoTool::getReferencedKeysInfo("ek_user"));
            //--------------------------------------------
            "links" => [
                [
                    /**
                     * Foreach ric,
                     * notice that we use the foreign key (seller_id) of the foreign table rather
                     * than the ric of the current table (id)
                     */
                    "link" => E::link("NullosAdmin_Ekom_TestHas_List") . "?seller_id=$id",
                    "text" => "Voir les addresses de ce vendeur",
                    "disabled" => !$isUpdate,
                ],
            ],
        ],
    ],
];