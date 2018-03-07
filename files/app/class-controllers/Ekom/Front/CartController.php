<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Model\Front\CartModel;

class CartController extends EkomFrontController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();


        $this->getClaws()
            ->setLayout('sandwich_1c/raw')
            ->setWidget("maincontent.cart", ClawsWidget::create()
                ->setTemplate('Ekom/Cart/leaderfit-cart')
                ->setConf(EkomApi::inst()->cartLayer()->getCartModel())
            );
    }

//    public function render()
//    {
//        return $this->renderByViewId("Ekom/cart");
//    }
}