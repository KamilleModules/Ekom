<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\EkomFrontController;

class AddressEditController extends EkomFrontController
{

    public function render()
    {
        return $this->renderByViewId("Ekom/customer/addressEdit");
    }
}