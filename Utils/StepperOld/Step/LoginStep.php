<?php


namespace Module\Ekom\Utils\Stepper\Step;


use Module\Ekom\Api\EkomApi;

class LoginStep extends Step
{

    protected $model;

    public function render()
    {
        $response = null;
        $model = EkomApi::inst()->connexionLayer()->handleLoginForm($response);
        return $model;
    }

    public function isPosted()
    {
        return array_key_exists('ekom-login-key', $_POST);
    }

    public function isValid()
    {
        $response = null;
        $model = EkomApi::inst()->connexionLayer()->handleLoginForm($response);
        return (null !== $response);
    }

    public function getData()
    {
        return [];
    }


}