<?php


namespace Controller\Ekom\Page;

use Controller\Ekom\EkomFrontController;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


class FormationCategoryPageController extends EkomFrontController
{

    public function render()
    {
        HtmlPageHelper::addBodyClass("page-category-formation");
        HtmlPageHelper::css("/css/page-category-formation.css");
        return $this->renderByViewId("Ekom/formationCategoryPage");
    }
}