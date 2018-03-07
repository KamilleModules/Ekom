<?php


use Bat\HashTool;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\SokoForm\Control\EkomSokoDateControl;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$value = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;

$genders = [
    1 => "Male",
    2 => "Female",
];


$form = SokoForm::create()
    ->setName("soko-form-user")
    ->addControl(SokoInputControl::create()
        ->setName("id")
        ->setLabel('Id')
        ->setProperties([
//                'disabled' => true,
            'readonly' => true,
        ])
        ->setValue($value)
    )
    ->addControl(SokoInputControl::create()
        ->setName("shop_id")
        ->setLabel('Shop id')
        ->setProperties([
            'readonly' => true,
        ])
        ->setValue($shopId)
    );

if (null === $value) { // insert
    $form
        ->addControl(SokoInputControl::create()
            ->setName("pass")
            ->setLabel('Password')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addValidationRule("pass", SokoNotEmptyValidationRule::create());

} else {
    $form->addControl(SokoInputControl::create()
        ->setName("pass")
        ->setLabel('Password')
        ->setProperties([
            'readonly' => true,
        ])
    );
}

$form
    ->addControl(SokoInputControl::create()
        ->setName("email")
        ->setLabel('Email')
        ->setProperties([
            'required' => true,
        ])
    )
    ->addControl(SokoInputControl::create()
        ->setName("pseudo")
        ->setLabel('Pseudo')
        ->setProperties([
            'required' => true,
        ])
    )
    ->addControl(SokoInputControl::create()
        ->setName("first_name")
        ->setLabel('First name')
    )
    ->addControl(SokoInputControl::create()
        ->setName("last_name")
        ->setLabel('Last name')
    )
    ->addControl(SokoInputControl::create()
        ->setName("mobile")
        ->setLabel('Mobile')
    )
    ->addControl(SokoInputControl::create()
        ->setName("phone")
        ->setLabel('Phone')
    )
    ->addControl(SokoInputControl::create()
        ->setName("phone_prefix")
        ->setLabel('Phone prefix')
    )
    ->addControl(SokoBooleanChoiceControl::create()
        ->setName("newsletter")
        ->setLabel('Newsletter')
        ->setValue(1)
    )
    ->addControl(SokoChoiceControl::create()
        ->setName("gender")
        ->setLabel('Gender')
        ->setChoices($genders)
    )
    ->addControl(EkomSokoDateControl::create()
        ->setName("birthday")
        ->setLabel('Birthday')
    )
    ->addControl(SokoBooleanChoiceControl::create()
        ->setName("active")
        ->setLabel('Active')
        ->setValue(1)
    )
    ->addValidationRule("email", SokoNotEmptyValidationRule::create())
    ->addValidationRule("pseudo", SokoNotEmptyValidationRule::create());


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "User",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'feed' => MorphicHelper::getFeedFunction("ek_user"),
    'process' => function ($fData, SokoFormInterface $form) use ($shopId) {

//        $hash = HashTool::getRandomHash64();
        $hash = "";
        if (empty($fData['id'])) {

            QuickPdo::insert("ek_user", [
                "shop_id" => $shopId,
                "email" => $fData['email'],
                "pass" => EkomApi::inst()->passwordLayer()->passEncrypt($fData['pass']),
                "pseudo" => $fData['pseudo'],
                "first_name" => $fData['first_name'],
                "last_name" => $fData['last_name'],
                "date_creation" => date("Y-m-d H:i:s"),
                "mobile" => $fData['mobile'],
                "phone" => $fData['phone'],
                "phone_prefix" => $fData['phone_prefix'],
                "newsletter" => $fData['newsletter'],
                "gender" => $fData['gender'],
                "birthday" => $fData['birthday'],
                "active_hash" => $hash,
                "active" => $fData['active'],
            ]);
            $form->addNotification("L'utilisateur a bien été ajouté", "success");
        } else {
            QuickPdo::update("ek_user", [
                "email" => $fData['email'],
                "pseudo" => $fData['pseudo'],
                "first_name" => $fData['first_name'],
                "last_name" => $fData['last_name'],
                "mobile" => $fData['mobile'],
                "phone" => $fData['phone'],
                "phone_prefix" => $fData['phone_prefix'],
                "newsletter" => $fData['newsletter'],
                "gender" => $fData['gender'],
                "birthday" => $fData['birthday'],
                "active" => $fData['active'],
            ], [
                ['id', '=', $fData['id']],
            ]);
            $form->addNotification("L'utilisateur a bien été mis à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [
                [
                    "link" => E::link("NullosAdmin_Ekom_UserHasAddress_List") . "?id=$value",
                    "text" => "Voir les addresses de cet utilisateur",
                ],
                [
                    "link" => E::link("NullosAdmin_Ekom_UserHasGroup_List") . "?id=$value",
                    "text" => "Voir les groupes de cet utilisateur",
                ],
            ],
        ],
    ],
];