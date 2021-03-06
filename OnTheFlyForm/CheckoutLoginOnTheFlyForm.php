<?php


namespace Module\Ekom\OnTheFlyForm;


use Module\Ekom\Utils\E;
use OnTheFlyForm\OnTheFlyForm;

class CheckoutLoginOnTheFlyForm extends OnTheFlyForm
{
    public function __construct()
    {
        parent::__construct();

        $this->setKey("checkout-login-key");

        $this->setIds([
            'email',
            'pass',
            'memorize',
        ]);
        $this->setSingleCheckboxes(['memorize']);
        $this->setConstants([
            "uriCreateAccount" => E::link("Ekom_createAccount"),
            "uriForgotPassword" => E::link("Ekom_forgotPassword"),
        ]);


        $this->setValidationRules([
            'email' => ['required'],
            'pass' => ['required'],
        ]);
    }


}