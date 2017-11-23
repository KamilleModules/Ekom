<?php


namespace Module\Ekom\Utils\Checkout\Step;


use Module\Ekom\Api\EkomApi;

class LoginCheckoutStep extends BaseCheckoutStep
{

    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->label = "Identification";
    }


    public function isSuccessfullyPosted()
    {
        $response = null;

        $this->model = EkomApi::inst()->connexionLayer()->handleLoginForm($response);
        if (null !== $response) {
            /**
             * But we need to reload the page for the connected action to take effect?
             */
            $this->stepData = [
                "login" => "ok",
            ];
            return true;
        }
        return false;
    }

    public function getFormModel()
    {
        return $this->model;
    }


}