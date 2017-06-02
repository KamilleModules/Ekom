<?php


namespace Controller\Ekom\Front;



use Controller\Ekom\EkomFrontController;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

class CategoryController extends EkomFrontController
{


    public function render()
    {
        return $this->renderByViewId("Ekom/category");
    }


//    public function render(){
//        HtmlPageHelper::addBodyClass("page-category");
//        HtmlPageHelper::css("/css/page-category.css");
//        return $this->renderByViewId("Ekom/categoryPage");
//    }

}