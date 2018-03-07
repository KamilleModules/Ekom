<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\UserGroupLayer;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Api\Object\UserHasUserGroup;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;


//--------------------------------------------
// FORM WITH CONTEXT
//--------------------------------------------
$id = MorphicHelper::getFormContextValue("id", $context); // userId
$userGroups = UserGroupLayer::getEntries();
$userGroupId = (array_key_exists("user_group_id", $_GET)) ? (int)$_GET['user_group_id'] : 0;


$isReadOnly = (0!==$userGroupId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "User has address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-user_has_address")
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel('User id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("user_group_id")
            ->setLabel('User group id')
            ->setChoices($userGroups)
            ->setValue($userGroupId)
            ->setProperties([
//                'disabled' => true,
                'readonly' => $isReadOnly,
            ])
        )
//        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => function (SokoFormInterface $form, array $ric) {
        $markers = [];
        $values = array_intersect_key($_GET, array_flip($ric));
        $q = "select * from ek_user_has_user_group";
        QuickPdoStmtTool::addWhereEqualsSubStmt($values, $q, $markers);
        $row = QuickPdo::fetch("$q", $markers);
        $form->inject($row);
    },
    'process' => function ($fData, SokoFormInterface $form) use ($userGroupId) {
        if (0 === $userGroupId) {
            UserHasUserGroup::getInst()->create($fData);
            $form->addNotification("Le groupe a bien été ajouté pour cet utilisateur", "success");
        } else {
            UserHasUserGroup::getInst()->update($fData, [
                "user_id" => $fData['user_id'],
                "user_group_id" => $fData['user_group_id'],
            ]);
            $form->addNotification("Le groupe a bien été mis à jour pour cet utilisateur", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'user_id',
        'user_group_id',
    ],
];




