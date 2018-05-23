<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\SokoForm\UserAddress\UserAddressSokoForm;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;
use SokoForm\Form\SokoFormInterface;

class SokoBillingCheckoutProcessStep extends BaseCheckoutProcessStep
{


    private $response;
    private $firstAddressForm;

    public function __construct()
    {
        parent::__construct();
        $this->response = null;
        $this->firstAddressForm = null;

    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {

        /**
         * If the firstAddress form is posted, we treat it.
         * Note however that it doesn't influence whether or not the billingStep isPostedSuccessfully
         */
        if (true === E::userIsConnected()) {
            $this->getFirstAddressForm()->process(function (array $filteredContext, SokoFormInterface $form) {
                try {
                    $userId = E::getUserId();
                    if (array_key_exists("is_default_billing_address", $filteredContext)) {
                        $filteredContext['is_default_billing_address'] = (int)$filteredContext['is_default_billing_address'];
                    }
                    if (array_key_exists("is_default_shipping_address", $filteredContext)) {
                        $filteredContext['is_default_shipping_address'] = (int)$filteredContext['is_default_shipping_address'];
                    }

                    EkomApi::inst()->userAddressLayer()->createAddress($userId, $filteredContext);
                } catch (EkomUserMessageException $e) {
                    $form->addNotification($e->getMessage(), "error");
                }
            });
        }


        if (array_key_exists("complete_billing_step", $context)) {
            $billingAddressId = (int)$this->getCurrentCheckoutData("billing_address_id");
            if (0 === $billingAddressId) {
                throw new EkomUserMessageException("Veuillez choisir une adresse de facturation");
            }
            return (0 !== $billingAddressId);
        }
        return false;
    }

    public function isValid()
    {
        return (0 !== (int)$this->getCurrentCheckoutData("billing_address_id"));
    }

    public function getModel()
    {
        /**
         * This will throw an exception an exception if the user is not connected.
         * This means this step ONLY WORKS once the user is logged in.
         */
        if (true === E::userIsConnected()) {

            $userId = E::getUserId();
            $addresses = UserAddressLayer::getUserAddresses($userId);

            $ret = [];
            $hasAddress = (count($addresses) > 0);
            $ret['hasAddress'] = $hasAddress;
            if ($hasAddress) {


                $selectedBillingAddressId = $this->getCurrentCheckoutData("billing_address_id");
                // which billing address?
                if (null === $selectedBillingAddressId) {
                    $selectedBillingAddress = UserAddressLayer::getPreferredBillingAddress($userId);
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
                $this->setCurrentCheckoutData("billing_address_id", $selectedBillingAddressId);
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