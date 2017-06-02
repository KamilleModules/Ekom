<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;

class CartController extends EkomFrontController
{

    public function render()
    {
        return $this->renderByViewId("Ekom/cart");
    }
}