<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;

class ForgotPasswordController extends EkomFrontController
{

    public function render()
    {
        return $this->renderByViewId("Ekom/forgotPassword");
    }
}