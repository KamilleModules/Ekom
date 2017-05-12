<?php


namespace Controller\Ekom\Page;



use Controller\Ekom\EkomFrontController;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

class ProductPageController extends EkomFrontController
{

    public function render(){


//        $f = "/myphp/leaderfit/theme/ultimo/pages/product.php";
//        $c = file_get_contents($f);
//        return HttpResponse::create($c);


        HtmlPageHelper::addBodyClass("page-product");
        HtmlPageHelper::css("/css/page-product.css");
        return $this->renderByViewId("Ekom/productPage");
    }
}