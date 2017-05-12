<?php


namespace Controller\Ekom\Page;



use Controller\Ekom\EkomFrontController;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

class CategoryPageController extends EkomFrontController
{

    public function render(){
        HtmlPageHelper::addBodyClass("page-category");
        HtmlPageHelper::css("/css/page-category.css");
        return $this->renderByViewId("Ekom/categoryPage");
    }
}