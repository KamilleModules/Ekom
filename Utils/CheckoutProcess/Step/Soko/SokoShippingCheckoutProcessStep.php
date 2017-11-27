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

class SokoShippingCheckoutProcessStep extends BaseCheckoutProcessStep
{

    private $model;
    private $response;
    private $shopId;
    private $langId;

    /**
     * @var SokoFormInterface
     */
    private $addressFormModel;

    public function __construct()
    {
        $this->addressFormModel = null;
        $this->response = null;
        $this->shopId = null;
        $this->langId = null;
    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {
        $this->shopId = $context['shop_id'];
        $this->langId = $context['lang_id'];



        return false;
    }

    public function isValid()
    {
        return false;
    }

    public function getModel()
    {
        /**
         * This will throw an exception an exception if the user is not connected.
         * This means this step ONLY WORKS once the user is logged in.
         */
        if (true === E::userIsConnected()) {

            $userId = E::getUserId();
            $addresses = UserAddressLayer::getUserAddresses($userId, $this->langId);
            $hasAddress = count($addresses) > 0;


            $carrierOffers = CartUtil::getCarrierOffers($this->shopId, $this->langId);


            $ret = [
                'userHasAddress' => $hasAddress,
            ];

            if (true === $hasAddress) {
                $ret['carrierOffers'] = $carrierOffers;
                $ret['userAddresses'] = $addresses;
            } else {
                $this->addressFormModel = UserAddressSokoForm::getForm();
                $ret['addressFormModel'] = $this->addressFormModel;
            }

            return $ret;


        } else {
            return ClawsWidgetError::create()
                ->setErrorMessage("The user is not connected")
                ->getModel();
        }
    }
}