<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;

class ProductCardController extends EkomFrontController
{


    /**
     *
     * This controller can render one of the following pages:
     *
     * - productCard
     * - productCardError
     *
     *
     * We try to render a product card by it's slug.
     * If the slug doesn't match, we display a productCardError page,
     * if it matches, we display a productCard page.
     *
     *
     */
//    public function render()
//    {
//
//
//
//
//        if (null !== ($slug = Z::getUrlParam('slug'))) {
//            if (false !== ($cardId = EkomApi::inst()->productLayer()->getProductCardIdBySlug($slug))) {
//
//                ApplicationRegistry::set("ekom.cardId", $cardId);
//                return $this->renderByViewId("Ekom/productCard");
//            }
//        }
//        if (true === ApplicationParameters::get("debug")) {
//            XLog::debug("[Ekom module] - ProductCardController: product not found with slug: $slug");
//        }
//        return $this->renderByViewId("Ekom/productCardError");
//    }

//    public function render(){
//
//
////        $f = "/myphp/leaderfit/theme/ultimo/pages/product.php";
////        $c = file_get_contents($f);
////        return HttpResponse::create($c);
//
//
//        HtmlPageHelper::addBodyClass("page-product");
//        HtmlPageHelper::css("/css/page-product.css");
//        return $this->renderByViewId("Ekom/productPage");
//    }
}