<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Module\Ekom\Model\Front\LoginFormModel\SokoLoginFormModel;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;

class SokoLoginCheckoutProcessStep extends BaseCheckoutProcessStep
{

    private $model;
    private $response;

    public function __construct()
    {
        parent::__construct();
        $this->model = null;
        $this->response = null;
    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {
        $this->getModel();
        return (null !== $this->response);
    }

    public function isValid()
    {
        return E::userIsConnected();
    }

    public function getModel()
    {
        if (null === $this->model) {
            $response = null;
            $this->model = SokoLoginFormModel::getFormModel($response);
            $this->response = $response;
        }
        return $this->model;
    }

    public function isSkipped()
    {
        return E::userIsConnected();
    }


}