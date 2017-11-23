<?php


namespace Module\Ekom\Utils\Checkout\Step;


use Core\Services\A;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use OnTheFlyForm\OnTheFlyFormInterface;

class ShippingCheckoutStep extends BaseCheckoutStep
{

    /**
     * @var $form OnTheFlyFormInterface
     */
    private $form;

    public function __construct()
    {
        parent::__construct();
        $this->label = "Adresse de livraison";
    }

    public function prepare(array $stepData, array $context)
    {
        $form = A::getOnTheFlyForm("ThisApp:CheckoutShipping");
        $rules = [
            'carrier_name' => ['required'],
            'shipping_address_id' => ['required'],
            'billing_address_id' => ['required'],
        ];
        $form->setValidationRules($rules);
        $this->form = $form;
    }


    public function isSuccessfullyPosted()
    {
        $form = $this->form;
        if (true === $form->isPosted()) {
            $form->inject($_POST);
            if (true === $form->validate()) {
                $data = $form->getData();
                if (array_key_exists('comment', $data)) {
                    EkomApi::inst()->checkoutLayer()->setShippingComment($data['comment']);
                }
                $this->stepData = $data;
            }
        } else {
            $form->inject($this->context);
        }
    }

    public function getFormModel()
    {

        $cartModel = EkomApi::inst()->cartLayer()->getCartModel();

        /**
         * take default values from the current session,
         * note that defaults values are not used in this case
         */
        $defaultValues = EkomApi::inst()->checkoutLayer()->getShippingInfo();
        $billingAddressId = $defaultValues['billing_address_id'];
        $shippingAddressId = $defaultValues['shipping_address_id'];
//            $shippingBillingSynced = $defaultValues['shipping_billing_synced'];
        $carrierName = $defaultValues['carrier_name'];
//        list($billingAddress, $shippingAddress, $userAddresses) = EkomApi::inst()->userAddressLayer()->getUserBillingShippingAndAddresses($billingAddressId, $shippingAddressId);
        $shippingAddress = ShopLayer::getPhysicalAddresses()[0];
        $billingAddress = $shippingAddress;
        $userAddresses = [];

        $cartItems = $cartModel['items'];
        $allCarriers = EkomApi::inst()->carrierLayer()->getAllCarriersShippingCost($cartItems, $shippingAddress);


        $form = $this->form;

        $conf = $form->getModel();
        $conf['allCarriers'] = $allCarriers;
//            $conf['carrierLabel'] = $carrierLabel;
//            $conf['shippingCost'] = $shippingCost;
//            $conf['estimatedDeliveryDate'] = $estimatedDeliveryDate;
        $conf['addresses'] = $userAddresses;
        $conf['shipping_address'] = $shippingAddress;
        $conf['billing_address'] = $billingAddress;

//            az($conf['addresses']);

        //--------------------------------------------
        // new address model
        //--------------------------------------------
        $naForm = A::getOnTheFlyForm("Ekom:UserAddress");
        if (true === $naForm->isPosted()) {
            throw new EkomException("This case should not happen (this controller wants it to be ajax driven)");
        } else { // initial form display
            $defaultValues = [
                'country_id' => EkomApi::inst()->countryLayer()->getCountryIdByIso("FR"),
            ];
            $naForm->inject($defaultValues);
        }
        $newAddressModel = $naForm->getModel();
        $conf['newAddressModel'] = $newAddressModel;


        $this->decorateModel($conf);
        return $conf;


    }



    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateModel(array &$model)
    {

    }
}