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
            "country_id",
            "phone",
            "supplement",
        ]);


        $countries = EkomApi::inst()->countryLayer()->getCountryList();
        $this->setOptions("country_id", $countries)
            ->setNotHtmlSpecialChars([
                'country_id',
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






//    protected function getBaseModel()
//    {
//        $countryId = (int)EkomApi::inst()->userLayer()->getUserPreferredCountry();
//        return [
//
//            "nameFirstName" => "first_name",
//            "nameLastName" => "last_name",
//            "nameAddress" => "address",
//            "namePostcode" => "postcode",
//            "nameCity" => "city",
//            "nameCountry" => "country_id",
//            "namePhone" => "phone",
//            "nameExtra" => "extra",
//            "nameIsPreferred" => "is_preferred",
//
//
//            "valueFirstName" => "",
//            "valueLastName" => "",
//            "valueAddress" => "",
//            "valuePostcode" => "",
//            "valueCity" => "",
//            "valueCountry" => $countryId,
//            "valuePhone" => "",
//            "valueExtra" => "",
//
//
//            "checkedIsPreferred" => "",
//            //
//            "errorFirstName" => "",
//            "errorLastName" => "",
//            "errorAddress" => "",
//            "errorPostcode" => "",
//            "errorCity" => "",
//            "errorCountry" => "",
//            "errorPhone" => "",
//        ];
//    }
//
//    protected function getField2Validators()
//    {
//        return [
//            'firstName' => ['required'],
//            'lastName' => ['required'],
//            'address' => ['required'],
//            'postcode' => ['required'],
//            'city' => ['required'],
//            'country' => ['required'],
//            'phone' => ['required'],
//        ];
//    }

}