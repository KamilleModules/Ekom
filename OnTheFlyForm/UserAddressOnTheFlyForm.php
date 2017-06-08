<?php


namespace Module\Ekom\OnTheFlyForm;


use Module\Ekom\Api\EkomApi;
use OnTheFlyForm\OnTheFlyForm;

class UserAddressOnTheFlyForm extends OnTheFlyForm
{


    protected function getBaseModel()
    {
        $countryId = (int)EkomApi::inst()->userLayer()->getUserPreferredCountry();
        return [

            "nameFirstName" => "first_name",
            "nameLastName" => "last_name",
            "nameAddress" => "address",
            "namePostcode" => "postcode",
            "nameCountry" => "country",
            "namePhone" => "phone",
            "nameExtra" => "extra",
            "nameIsPreferred" => "is_preferred",


            "valueFirstName" => "pierre",
            "valueLastName" => "",
            "valueAddress" => "",
            "valuePostcode" => "",
            "valueCountry" => $countryId,
            "valuePhone" => "",
            "valueExtra" => "",


            "checkedIsPreferred" => "",
            //
//            "errorFirstName" => "",
            "errorLastName" => "",
            "errorAddress" => "",
            "errorPostcode" => "",
            "errorCountry" => "",
            "errorPhone" => "",
        ];
    }

    protected function getField2Validators()
    {
        return [
            'firstName' => ['required'],
            'lastName' => ['required'],
            'address' => ['required'],
            'postcode' => ['required'],
            'country' => ['required'],
            'phone' => ['required'],
        ];
    }

}