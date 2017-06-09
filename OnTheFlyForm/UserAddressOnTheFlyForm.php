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
            "nameCity" => "city",
            "nameCountry" => "country_id",
            "namePhone" => "phone",
            "nameExtra" => "extra",
            "nameIsPreferred" => "is_preferred",


            "valueFirstName" => "pierre",
            "valueLastName" => "",
            "valueAddress" => "",
            "valuePostcode" => "",
            "valueCity" => "",
            "valueCountry" => $countryId,
            "valuePhone" => "",
            "valueExtra" => "",


            "checkedIsPreferred" => "",
            //
            "errorFirstName" => "",
            "errorLastName" => "",
            "errorAddress" => "",
            "errorPostcode" => "",
            "errorCity" => "",
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
            'city' => ['required'],
            'country' => ['required'],
            'phone' => ['required'],
        ];
    }

}