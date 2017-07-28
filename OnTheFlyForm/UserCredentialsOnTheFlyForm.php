<?php


namespace Module\Ekom\OnTheFlyForm;


use Module\Ekom\Api\EkomApi;
use OnTheFlyForm\DataAdaptor\DataAdaptor;
use OnTheFlyForm\OnTheFlyForm;

class UserCredentialsOnTheFlyForm extends OnTheFlyForm
{
    public function __construct()
    {
        parent::__construct();
        $this->setKey("account-user-credentials");
        $this->setIds([
            "current_pass",
            "pass",
            "pass_confirm",
        ]);

        $this
            ->setValidationRules([
                'current_pass' => ["required"],
                'pass' => ["required", 'minLength:3'],
                'pass_confirm' => ["required", 'sameAs:pass'],
            ]);


    }
}