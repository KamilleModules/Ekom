<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Core\Services\Hooks;
use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\StoreLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Helper\CheckoutProcessHelper;
use Module\Ekom\SokoForm\UserAddress\UserAddressSokoForm;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;
use SokoForm\Form\SokoFormInterface;

class SokoShippingCheckoutProcessStep extends BaseCheckoutProcessStep
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
         * Note however that it doesn't influence whether or not the shippingStep isPostedSuccessfully
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

        if (array_key_exists("complete_shipping_step", $context)) {
            $billingAddressId = (int)$this->getCurrentCheckoutData("billing_address_id");
            if (0 === $billingAddressId) {
                throw new EkomUserMessageException("Veuillez choisir une adresse de facturation");
            }

            $shippingAddressId = (int)$this->getCurrentCheckoutData("shipping_address_id");
            if (0 === $shippingAddressId) {
                throw new EkomUserMessageException("Veuillez choisir une adresse de livraison");
            }
            return (
                null !== $this->getCurrentCheckoutData("carrier_id") &&
                null !== $this->getCurrentCheckoutData("shipping_address_id") &&
                null !== $this->getCurrentCheckoutData("billing_address_id") &&
                null !== $this->getCurrentCheckoutData("shipping_comments")
            );
        }
        return false;
    }

    public function isValid()
    {
        return (
            null !== $this->getCurrentCheckoutData("carrier_id") &&
            0 !== (int)$this->getCurrentCheckoutData("shipping_address_id") &&
            0 !== (int)$this->getCurrentCheckoutData("billing_address_id") &&
            null !== $this->getCurrentCheckoutData("shipping_comments")
        );
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

                $billing_synced_with_shipping = (bool)$this->getCurrentCheckoutData("billing_synced_with_shipping", false);
                $shippingComments = $this->getCurrentCheckoutData("shipping_comments", "");


                $carrierOffers = $this->collectCarrierOffers();


                if (0 === count($carrierOffers)) {
                    throw new EkomUserMessageException("No carrier offer for this app");
                }


                CheckoutProcessHelper::fixUnsyncedCurrentCheckoutDataAddresses();


                /**
                 * Note: it's important for me to be sure selectedCarrierId is defined.
                 * That's because if we click the next step button without having
                 * a carrier selected, this might cause some confusion.
                 */
                $selectedCarrierId = null;
                foreach ($carrierOffers as $carrierId => $carrierOffer) {
                    if (true === $carrierOffer['selected']) {
                        $selectedCarrierId = $carrierId;
                        break;
                    }
                }

                /**
                 * If no carrier was selected, we choose the first one by default
                 * (assuming that the preferred carrier is on the top)
                 */
                if (null === $selectedCarrierId) {
                    reset($carrierOffers);
                    $selectedCarrierId = key($carrierOffers);
                    $carrierOffers[$selectedCarrierId]['selected'] = true;
                }
                $ret['carrierOffers'] = $carrierOffers;
//                az($carrierOffers);

                /**
                 * Sometimes, you only have one carrierOffer and the snippet above
                 * didn't match any carrierOffer so your selectedCarrierId is null,
                 * let's fix this, as there must be one selected carrier offer (I say).
                 * Edit: actually useless snippet below, but just in case...
                 */
//                if(null===$selectedCarrierId){
//                    $selectedCarrierId = key($carrierOffers);
//                }


                // which one is selected in the gui?
                $selectedAddressId = $this->getCurrentCheckoutData("shipping_address_id");
                if (null === $selectedAddressId) {
                    $selectedAddress = UserAddressLayer::getPreferredShippingAddress($userId);
                    $selectedAddressId = $selectedAddress['address_id'];
                }

                // which billing address?
                $selectedBillingAddressId = $this->getCurrentCheckoutData("billing_address_id");
                if (null === $selectedBillingAddressId) {
                    $selectedBillingAddress = UserAddressLayer::getPreferredBillingAddress($userId);
                    $selectedBillingAddressId = $selectedBillingAddress['address_id'];
                }

                $selectedAddressId = (int)$selectedAddressId;
                $selectedBillingAddressId = (int)$selectedBillingAddressId;
                $shippingAddress = null;
                $billingAddress = null;

                $userAddresses = $addresses;
                foreach ($userAddresses as $k => $address) {
                    $address['selected'] = ($selectedAddressId === (int)$address['address_id']);
                    $userAddresses[$k] = $address;

                    if ($selectedAddressId === (int)$address['address_id']) {
                        $shippingAddress = $address;
                    }

                    if ($selectedBillingAddressId === (int)$address['address_id']) {
                        $billingAddress = $address;
                    }
                }


                Hooks::call("Ekom_CheckoutProcess_SokoShipping_setBillingSyncedWithShipping", $billing_synced_with_shipping, $selectedCarrierId, $carrierOffers);


                if (true === $billing_synced_with_shipping) {
                    $billingAddress = $shippingAddress;
                }


                $this->debug("SokoShippingCheckoutProcessStep: selectedCarrierId=$selectedCarrierId, selectedAddressId=$selectedAddressId, selectedBillingAddressId=$selectedBillingAddressId");
                $this->setCurrentCheckoutData("billing_address_id", $selectedBillingAddressId);
                $this->setCurrentCheckoutData("shipping_address_id", $selectedAddressId);
                $this->setCurrentCheckoutData("carrier_id", $selectedCarrierId);


                $ret['userAddresses'] = $userAddresses;
                $ret['billingSyncedWithShippingValue'] = (int)!$billing_synced_with_shipping;
                $ret['billingSyncedWithShippingSelected'] = $billing_synced_with_shipping;
                $ret['shippingAddress'] = $shippingAddress;
                $ret['billingAddress'] = $billingAddress;
                $ret['carrierId'] = (string)$selectedCarrierId;
                /**
                 * Watch out, this could be an int or null.
                 */
                $ret['storeAddressId'] = StoreLayer::getPreferredPhysicalAddressIdById($this->getCurrentCheckoutData("store_address_id"));
                $ret['shippingComments'] = $shippingComments;
                $ret['context'] = $this->context;
            } else {
                $firstAddressForm = $this->getFirstAddressForm();


                $context = [];
                Hooks::call("Ekom_Front_decorateFetchAddressContext", $context);
                $firstAddressForm->inject($context);


                $ret['form'] = $firstAddressForm->getModel();
            }

            return $ret;
        } else {
            return ClawsWidgetError::create()
                ->setErrorMessage("The user is not connected")
                ->getModel();
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function collectCarrierOffers()
    {
        $currentCheckoutDataModel = CurrentCheckoutData::all();
        $cartModel = CartLayer::create()->getCartModel();
        return CartUtil::getCarrierOffers($cartModel, $currentCheckoutDataModel);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getFirstAddressForm()
    {
        if (null === $this->firstAddressForm) {
            $this->firstAddressForm = UserAddressSokoForm::getForm("SokoShippingCheckoutProcessStep");
        }
        return $this->firstAddressForm;
    }


    private function debug($msg)
    {
        E::dlog($msg);
    }
}