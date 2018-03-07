<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\AddressListFormatter\AddressListFormatter;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use OnTheFlyForm\OnTheFlyForm;

class AddressBookControllerOld extends CustomerController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $userId = E::getUserId();
        $addressList = EkomApi::inst()->userAddressLayer()->getUserAddresses($userId);
        $formatter = new AddressListFormatter();
        $addressListModel = $formatter->format($addressList);


        //--------------------------------------------
        // @ON THE FLY FORM DOC
        //--------------------------------------------
        $form = A::getOnTheFlyForm("Ekom:UserAddress");


        if (true === $form->isPosted()) { // the form was posted
            throw new EkomException("This case should not happen (this controller wants it to be ajax driven)");
        } else { // initial form display

            $defaultValues = [
                'country_id' => EkomApi::inst()->countryLayer()->getCountryIdByIso(E::conf("countryIso")),
            ];
            $form->inject($defaultValues);
        }


        $newAddressModel = $form->getModel(); // see form onTheFlyForm model for more details


        $this->getClaws()
            ->setWidget("maincontent.addressBook", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/AddressBook/default")
                ->setConf([
                    'addressList' => $addressListModel,
                    'm:newAddressModel' => $newAddressModel,
                ])
            );
    }


    /**
     * @deprecated
     */
//    public function connectedRender()
//    {
//
//        $userId = E::getUserId();
//        $addressList = EkomApi::inst()->userAddressLayer()->getUserAddresses($userId);
//        $formatter = new AddressListFormatter();
//        $addressListModel = $formatter->format($addressList);
//
//
//        //--------------------------------------------
//        // @ON THE FLY FORM DOC
//        //--------------------------------------------
//        $form = A::getOnTheFlyForm("Ekom:UserAddress");
//
//
//        if (true === $form->isPosted()) { // the form was posted
//            throw new EkomException("This case should not happen (this controller wants it to be ajax driven)");
//        } else { // initial form display
//            $defaultValues = [
//                'country_id' => EkomApi::inst()->countryLayer()->getCountryIdByIso("FR"),
//            ];
//            $form->inject($defaultValues);
//        }
//
//
//        $newAddressModel = $form->getModel(); // see form onTheFlyForm model for more details
//
//
//        //--------------------------------------------
//        // END OF ON THE FLY FORM DOC
//        //--------------------------------------------
//        return $this->renderByViewId("Ekom/customer/addressBook", LawsConfig::create()->replace([
//            'widgets' => [
//                'maincontent.addressBook' => [
//                    'conf' => [
//                        'addressList' => $addressListModel,
//                        'm:newAddressModel' => $newAddressModel,
//                    ],
//                ],
//            ],
//        ]));
//    }
}