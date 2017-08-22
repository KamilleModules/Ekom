<?php


namespace Module\Ekom\OnTheFlyForm;


use Module\Ekom\Utils\E;
use OnTheFlyForm\OnTheFlyForm;

class CheckoutLoginOnTheFlyForm extends OnTheFlyForm
{
    public function __construct()
    {
        parent::__construct();
        $this->setIds([
            'email',
            'pass',
            'memorize',
        ]);
        $this->setSingleCheckboxes(['memorize']);
        $this->setImmutableValues([
            "uriCreateAccount" => E::link("Ekom_createAccount"),
            "uriForgotPassword" => E::link("Ekom_forgotPassword"),
        ]);
    }


}