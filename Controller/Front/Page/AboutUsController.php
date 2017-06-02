<?php


namespace Controller\Ekom\Front\Page;


use Controller\Ekom\EkomFrontController;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

class AboutUsController extends EkomFrontController
{


    public function render()
    {
        return $this->renderByViewId("Ekom/page/aboutUs");
    }
}