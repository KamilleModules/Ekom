<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\SokoForm\UserAddress\UserAddressSokoForm;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoFormInterface;

class SokoShippingCheckoutProcessStep extends BaseCheckoutProcessStep
{


    private $response;
    private $shopId;
    private $langId;
    private $firstAddressForm;

    public function __construct()
    {
        parent::__construct();
        $this->response = null;
        $this->shopId = null;
        $this->langId = null;
        $this->firstAddressForm = null;

    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {
        $this->shopId = $context['shop_id'];
        $this->langId = $context['lang_id'];

        /**
         * If the firstAddress form is posted, we treat it.
         * Note however that it doesn't influence whether or not the shippingStep isPostedSuccessfully
         */
        if (true === E::userIsConnected()) {
            $this->getFirstAddressForm()->process(function (array $filteredContext, SokoFormInterface $form) {
                try {
                    $userId = E::getUserId();
                    EkomApi::inst()->userAddressLayer()->createAddress($userId, $filteredContext);
                } catch (EkomUserMessageException $e) {
                    $form->addNotification($e->getMessage(), "error");
                }
            });
        }

        // theOtherForm->processValid
        return false;
    }

    public function isValid()
    {
        $shippingAddressId = CurrentCheckoutData::getShippingAddressId();
        $shippingComment = CurrentCheckoutData::getShippingAddressId();
        if(null===$shippingAddressId){

        }
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

            $ret = [];
            $hasAddress = (count($addresses) > 0);
            $ret['hasAddress'] = $hasAddress;
            if ($hasAddress) {
                $carrierOffers = CartUtil::getCarrierOffers($this->shopId, $this->langId);
                $ret['carrierOffers'] = $carrierOffers;

                // which one is selected in the gui?
                $selectedAddressId = CurrentCheckoutData::getShippingAddressId();
                if (null === $selectedAddressId) {
                    $selectedAddress = UserAddressLayer::getPreferredShippingAddress($userId, $this->langId);
                    $selectedAddressId = $selectedAddress['address_id'];
                }

                $selectedAddressId = (int)$selectedAddressId;

                $userAddresses = $addresses;
                foreach ($userAddresses as $k => $address) {
                    $address['selected'] = ($selectedAddressId === (int)$address['address_id']);
                    $userAddresses[$k] = $address;
                }
                $ret['userAddresses'] = $userAddresses;
                $ret['context'] = $this->context;
            } else {
                $firstAddressForm = $this->getFirstAddressForm();
                $ret['form'] = $firstAddressForm->getModel();
            }

            return $ret;
        } else {
            return ClawsWidgetError::create()
                ->setErrorMessage("The user is not connected")
                ->getModel();
        }
    }


    private function getFirstAddressForm()
    {
        if (null === $this->firstAddressForm) {
            $this->firstAddressForm = UserAddressSokoForm::getForm();
//            $this->firstAddressForm->addControl(SokoInputControl::create()->setName("hint")->setType("hidden")->setValue("firstAddress"));
        }
        return $this->firstAddressForm;
    }
}