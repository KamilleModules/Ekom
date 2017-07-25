<?php


namespace Module\Ekom\OnTheFlyForm;


use Module\Ekom\Api\EkomApi;
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
            "country",
            "phone",
            "supplement",
        ]);


        $countries = EkomApi::inst()->countryLayer()->getCountryList();
        $this->setOptions("country", $countries)
            ->setNotHtmlSpecialChars([
                'country',
            ])
            ->setValidationRules([
                'first_name' => ["required"],
                'last_name' => ["required"],
                'address' => ["required"],
                'postcode' => ["required"],
                'city' => ["required"],
                'country' => ["required"],
                'phone' => ["required"],
            ]);


    }
}