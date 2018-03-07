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

class AddressBookController extends CustomerController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $userId = E::getUserId();
        $addressList = EkomApi::inst()->userAddressLayer()->getUserAddresses($userId);
        $formatter = new AddressListFormatter();
        $addressListModel = $formatter->format($addressList);


        $this->getClaws()
            ->setWidget("maincontent.addressBook", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/AddressBook/default")
                ->setConf([
                    'addressList' => $addressListModel,
                ])
            );
    }
}