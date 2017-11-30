<?php

namespace Module\Ekom\SokoForm\UserAddress;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\CountryLayer;
use Module\Ekom\SokoForm\EkomSokoForm;
use Module\Ekom\Utils\E;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoInArrayValidationRule;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;

class UserAddressSokoForm
{

    /**
     * @return SokoFormInterface
     */
    public static function getForm($name = null)
    {


        if (null === $name) {
            $name = "UserAddressSokoForm";
        }

        /**
         * @todo-ling: add validation rules
         */
        $countryChoices = self::getCountryChoices();


        $isDefaultShippingChoice = [
            "1" => "En faire mon adresse de livraison par défaut",
        ];
        $isDefaultBillingChoice = [
            "1" => "En faire mon adresse de facturation par défaut",
        ];

        $form = EkomSokoForm::create()
            ->setName($name)
            ->addControl(SokoInputControl::create()
                ->setName("first_name")
                ->setLabel('Prénom')
            )
            ->addControl(SokoInputControl::create()
                ->setName("last_name")
                ->setLabel('Nom')
            )
            ->addControl(SokoInputControl::create()
                ->setLabel("Préfixe téléphonique")
                ->setValue("33")
                ->setName("phone_prefix") // a custom widget
            )
            ->addControl(SokoInputControl::create()
                ->setLabel('Numéro de téléphone')
                ->setName("phone")
            )
            ->addControl(SokoInputControl::create()
                ->setName("city")
                ->setLabel('Ville')
            )
            ->addControl(SokoInputControl::create()
                ->setName("address")
                ->setLabel('Adresse')
            )
            ->addControl(SokoInputControl::create()
                ->setName("postcode")
                ->setLabel('Code postal')
            )
            ->addControl(SokoInputControl::create()
                ->setName("supplement")
                ->setLabel('Supplément')
            )
            ->addControl(SokoChoiceControl::create()
                ->setName("country_id")
                ->setLabel('Pays de résidence')
                ->setValue(CountryLayer::getCountryIdByIso("FR"))
                ->setChoices($countryChoices)
            )
            ->addControl(SokoChoiceControl::create()
                ->setName("is_default_shipping_address")
                ->setChoices($isDefaultShippingChoice)
            )
            ->addControl(SokoChoiceControl::create()
                ->setName("is_default_billing_address")
                ->setChoices($isDefaultBillingChoice)
            );


        $form
            ->addValidationRule("first_name", SokoNotEmptyValidationRule::create())
            ->addValidationRule("last_name", SokoNotEmptyValidationRule::create())
            ->addValidationRule("address", SokoNotEmptyValidationRule::create())
            ->addValidationRule("city", SokoNotEmptyValidationRule::create())
            ->addValidationRule("postcode", SokoNotEmptyValidationRule::create())
            /**
             * @todo-ling: allow modules to decide their phone prefixes and country (rush now)
             */
            ->addValidationRule("phone_prefix", SokoInArrayValidationRule::create()->setArray(["33", "32"]))
            ->addValidationRule("phone", SokoNotEmptyValidationRule::create())
            ->addValidationRule("country", SokoInArrayValidationRule::create()
                ->setErrorMessage("Veuillez choisir un pays")
                ->setArray($countryChoices)
            );

        return $form;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getCountryChoices($langId = null)
    {
        $langId = E::getLangId($langId);
        $list = EkomApi::inst()->countryLayer()->getCountryList($langId);
        array_unshift($list, "Veuillez choisir un pays");
        return $list;
    }
}