<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;

class HomeController extends EkomFrontController
{

    public function render()
    {
        return $this->renderByViewId("Ekom/home");
    }
}