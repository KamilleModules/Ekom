<?php


namespace Module\Ekom\OnTheFlyForm;


use Module\Ekom\Api\EkomApi;
use OnTheFlyForm\DataAdaptor\DataAdaptor;
use OnTheFlyForm\OnTheFlyForm;

class UserAddressOnTheFlyForm extends OnTheFlyForm
{
    public function __construct()
    {
        parent::__construct();
        $this->setIds([
            "first_name",
            "last_name",
            "address",
            "postcode",
            "city",
            "country_id",
            "phone",
            "supplement",
            "is_default_shipping_address",
            "is_default_billing_address",
        ]);


        $countries = EkomApi::inst()->countryLayer()->getCountryList();
        $this->setOptions("country_id", $countries)
            ->setNotHtmlSpecialChars([
                'country_id',
            ])
            ->setSingleCheckboxes([
                "is_default_shipping_address",
                "is_default_billing_address",
            ])
            ->setValidationRules([
                'first_name' => ["required"],
                'last_name' => ["required"],
                'address' => ["required"],
                'postcode' => ["required"],
                'city' => ["required"],
                'country_id' => ["required"],
                'phone' => ["required"],
            ]);


    }
}