<?php


namespace Controller\Ekom\Front;


use Authenticate\SessionUser\SessionUser;
use Bat\UriTool;
use Controller\Ekom\EkomFrontController;
use Core\Services\Hooks;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Services\XLog;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class ProductCardControllerCopy extends EkomFrontController
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
    public function render()
    {

        EkomApi::inst()->initWebContext();


        if (null !== ($slug = Z::getUrlParam('slug'))) {
            if (false !== ($cardId = EkomApi::inst()->productLayer()->getProductCardIdBySlug($slug))) {

                ApplicationRegistry::set("ekom.cardId", $cardId);
                $ref = Z::getUrlParam('ref');
                ApplicationRegistry::set("ekom.productRef", $ref);


                $model = EkomApi::inst()->productLayer()->getProductBoxModel();


                $productId = $model['product_id'];

                //--------------------------------------------
                // seo
                //--------------------------------------------
                E::seo($model['metaTitle'], $model['metaDescription'], $model['metaKeywords']);

                $isConnected = SessionUser::isConnected();


                //--------------------------------------------
                // features
                //--------------------------------------------
                $features = EkomApi::inst()->featureLayer()->getFeaturesModelByProductId($productId);
                $technicalDescription = "";
                if (count($features) > 0) {
                    $technicalDescription = $features[0]['technical_description'];
                }


                //--------------------------------------------
                // bundles
                //--------------------------------------------
                $bundles = EkomApi::inst()->bundleLayer()->getBundleModelByProductId($productId);
                $hasBundle = (count($bundles) > 0);


                //--------------------------------------------
                // comments
                //--------------------------------------------
                $page = (array_key_exists('c-page', $_GET)) ? $_GET['c-page'] : 1;
                $sort = (array_key_exists('c-sort', $_GET)) ? $_GET['c-sort'] : 'date';
                $sortDir = (array_key_exists('c-sort-dir', $_GET)) ? $_GET['c-sort-dir'] : 'desc';

                $options = [
                    'page' => $page,
                    'sort' => $sort,
                    'sort-dir' => $sortDir,
                ];
                $comments = EkomApi::inst()->productCommentLayer()->getCommentsByProductId($model['product_id'], null, $options);
                $hash = "widget-product-comments";

                $nbComments = count($comments);
                $sum = 0;
                $avg = 0;
                foreach ($comments as $c) {
                    $sum += $c['rating'];
                }
                if ($nbComments > 0) {
                    $avg = $sum / $nbComments;
                }
                $uriPattern = UriTool::uri(null, [
                        'c-sort' => '_CSORT_',
                        'c-sort-dir' => '_CSORTDIR_',
                        'c-page' => 1,
                    ], false) . '#' . $hash;


                //--------------------------------------------
                // rendering the view
                //--------------------------------------------
                $productType = $model['product_type'];

                $lawsConfig = LawsConfig::create()
                    ->replaceWidgetTemplate("maincontent.productBox", function ($template) use ($productType) {
                        if ('default' === $productType || '' === (string)$productType) {
                            return $template;
                        }
                        return $template . '-' . $productType;
                    })
                    ->replace([
                        "widgets" => [
                            'maincontent.productBox' => [
                                "conf" => $model,
                            ],
                            'maincontent.features' => [
                                "conf" => [
                                    "technical_description" => $technicalDescription,
                                    "features" => $features,
                                ],
                            ],
                            'maincontent.productFeaturesBar' => [
                                "conf" => [
                                    'hasBundle' => $hasBundle,
                                ],
                            ],
                            'maincontent.bundles' => [
                                "conf" => [
                                    "product_id" => $productId,
                                    "bundles" => $bundles,
                                ],
                            ],
                            'maincontent.comments' => [
                                "conf" => [
                                    "product_id" => $productId,
                                    "nbComments" => $nbComments,
                                    "isConnected" => $isConnected,
                                    "currentSort" => $sort,
                                    "uriLogin" => E::link("Ekom_login") . "?hash=$hash",
                                    "uriSortByDateDesc" => $this->getCommentUri($uriPattern, 'date', 'desc'),
                                    "uriSortByRatingDesc" => $this->getCommentUri($uriPattern, 'rating', 'desc'),
                                    "uriSortByUsefulnessDesc" => $this->getCommentUri($uriPattern, 'useful', 'desc'),
                                    "comments" => $comments,
                                    "average" => $avg,
                                ],
                            ],
                        ],
                    ]);
                if (false === $hasBundle) {
                    $lawsConfig->removeWidget('maincontent.bundles');
                }
                return $this->renderByViewId("Ekom/productCard", $lawsConfig);

            }
        }
        if (true === ApplicationParameters::get("debug")) {
            XLog::debug("[Ekom module] - ProductCardController: product not found with slug: $slug");
        }
        return $this->renderByViewId("Ekom/productCardError");
    }

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

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getCommentUri($uriPattern, $sort, $sortDir)
    {
        return str_replace([
            '_CSORT_',
            '_CSORTDIR_',
        ], [
            $sort,
            $sortDir,
        ], $uriPattern);
    }

}