<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Module\Ekom\Model\Front\LoginFormModel\SokoLoginFormModel;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;

class SokoShippingCheckoutProcessStep extends BaseCheckoutProcessStep
{

    private $model;
    private $response;

    public function __construct()
    {
        $this->model = null;
        $this->response = null;
    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {
        return false;
    }

    public function isValid()
    {
        return false;
    }

    public function getModel()
    {
        return [];
    }
}