<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Utils\E;

class HomeController extends EkomFrontController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        HtmlPageHelper::addBodyClass("page-home");


        $productBoxes = ProductBoxLayer::getProductBoxListByGroupName('homePage');


        $this->getClaws()
            ->setLayout('sandwich_1c/default')
            ->setWidget("maincontent.mainSlider", ClawsWidget::create()
                ->setTemplate('Ekom/MainSlider/default')
                ->setConf([
                    "linkSlideOne" => [
                        "label" => "Corde",
                        "link" => E::link("Ekom_category", [
                            "slug" => "corde",
                        ]),
                    ],
                    "linkSlideTwo" => [
                        "label" => "Kettle bell",
                        "link" => E::link("Ekom_category", [
                            "slug" => "kettlebell",
                        ]),
                    ],
                    "linkSlideThree" => [
                        "label" => "Training",
                        "link" => E::link("Ekom_category", [
                            "slug" => "training",
                        ]),
                    ],
                ])
            )
            ->setWidget("maincontent.twoBanners", ClawsWidget::create()
                ->setTemplate('Ekom/TwoBanners/prototype')
            )
            ->setWidget("maincontent.banner", ClawsWidget::create()
                ->setTemplate('Ekom/Banner/prototype')
            )
            ->setWidget("maincontent.banner2", ClawsWidget::create()
                ->setTemplate('Ekom/Banner/prototype2')
            )
            ->setWidget("maincontent.featuredProducts", ClawsWidget::create()
                ->setTemplate('Ekom/CarouselProducts/default')
                ->setConf([
                    "title" => "NOTRE SÉLECTION",
                    "products" => $productBoxes,
                ])
            );
    }

//
//    public function render()
//    {
//        return $this->renderByViewId("Ekom/home");
//    }
}