<?php


namespace Controller\Ekom\Page;


use Controller\Ekom\EkomFrontController;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


class HomePageController extends EkomFrontController
{

    public function render(){
        HtmlPageHelper::addBodyClass("page-home");
        return $this->renderByViewId("Ekom/homePage");
    }
}