<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\SokoForm\UserAddress\UserAddressSokoForm;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;
use SokoForm\Form\SokoFormInterface;

class SokoFirstShippingAddressCheckoutProcessStep extends BaseCheckoutProcessStep
{

    /**
     * @var SokoFormInterface
     */
    private $form;
    private $response;
    private $shopId;
    private $langId;


    public function __construct()
    {
        $this->form = null;
        $this->response = null;
        $this->shopId = null;
        $this->langId = null;
    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {
        $this->shopId = $context['shop_id'];
        $this->langId = $context['lang_id'];
        $this->form = UserAddressSokoForm::getForm();
        $isValid = false;
        $this->form->process(function () use (&$isValid) {
            $isValid = true;
        });
        return $isValid;
    }

    public function isValid()
    {
        $isValid = false;
        if (true === E::userIsConnected()) {

            $userId = E::getUserId();
            $addresses = UserAddressLayer::getUserAddresses($userId, $this->langId);
            $isValid = count($addresses) > 0;
        }
        return $isValid;
    }

    public function getModel()
    {
        return $this->form->getModel();
    }
}