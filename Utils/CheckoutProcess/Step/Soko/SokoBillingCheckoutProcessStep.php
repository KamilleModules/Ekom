<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\SokoForm\UserAddress\UserAddressSokoForm;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;
use SokoForm\Form\SokoFormInterface;

class SokoBillingCheckoutProcessStep extends BaseCheckoutProcessStep
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

        if (array_key_exists("complete_billing_step", $context)) {
            return (null !== CurrentCheckoutData::get("billing_address_id"));
        }
        return false;
    }

    public function isValid()
    {
        return (null !== CurrentCheckoutData::get("billing_address_id"));
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


                $selectedBillingAddressId = CurrentCheckoutData::getBillingAddressId();
                // which billing address?
                if (null === $selectedBillingAddressId) {
                    $selectedBillingAddress = UserAddressLayer::getPreferredBillingAddress($userId, $this->langId);
                    $selectedBillingAddressId = $selectedBillingAddress['address_id'];
                }

                $selectedBillingAddressId = (int)$selectedBillingAddressId;
                $billingAddress = null;

                $userAddresses = $addresses;
                foreach ($userAddresses as $k => $address) {
                    $address['selected'] = ($selectedBillingAddressId === (int)$address['address_id']);
                    $userAddresses[$k] = $address;

                    if ($selectedBillingAddressId === (int)$address['address_id']) {
                        $billingAddress = $address;
                    }
                }

                $ret['userAddresses'] = $userAddresses;
                $ret['billingAddress'] = $billingAddress;
                $ret['context'] = $this->context;
                CurrentCheckoutData::setBillingAddressId($selectedBillingAddressId);
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
            $this->firstAddressForm = UserAddressSokoForm::getForm("SokoBillingCheckoutProcessStep");
        }
        return $this->firstAddressForm;
    }
}