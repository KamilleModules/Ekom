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
use Kamille\Utils\Claws\Claws;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\ProductBoxEntityUtil;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Model\Front\ProductPageModel;
use Module\Ekom\Utils\E;
use Module\EkomTrainingProducts\Api\EkomTrainingProductsApi;
use Module\ThisApp\Helper\ThisAppHelper;

class ProductCardController extends EkomFrontController
{


    protected function prepareClaws()
    {

        $ref = Z::getUrlParam('ref'); // product reference
        $model = ProductPageModel::getModelByProductReference($ref);
        $head = $model['head'];
        $tail = $model['tail'];






        if (false !== ($productId = ProductLayer::getProductIdByRef($ref))) {

            $productDetails = ProductBoxEntityUtil::filterProductDetails($_GET);
            $model = ProductBoxLayer::getProductBoxByProductId($productId, $productDetails);
            $this->prepareClawsByProductBoxModel($model);
        } else {
            if (true === ApplicationParameters::get("debug")) {
                XLog::debug("[Ekom module] - ProductCardController: product not found with ref: $ref");
            }
            $this->productNotFound();
        }
    }


    protected function prepareClawsByProductBoxModel(array $model)
    {
        parent::prepareClaws();
        EkomApi::inst()->initWebContext();
        $claws = $this->getClaws();
        if (
            array_key_exists("errorCode", $model) &&
            array_key_exists("errorMessage", $model)
        ) {
            $claws->setLayout("sandwich_1c/default");
            $this->productError($model);

        } else {
            $claws->setLayout("sandwich_1c/default");
            $this->collectWidgets($claws, $model);
        }
    }


    protected function productNotFound()
    {
        $this->getClaws()
            ->setWidget("maincontent.productCardNotFound", ClawsWidget::create()
                ->setTemplate("Ekom/Product/ProductCardNotFound/default")
            );
    }

    protected function productError(array $model)
    {
        $this->getClaws()
            ->setWidget("maincontent.productCardError", ClawsWidget::create()
                ->setTemplate("Ekom/Product/ProductCardError/default")
                ->setConf($model)
            );
    }


    protected function collectWidgets(Claws $claws, $model)
    {


        //--------------------------------------------
        // BRRR, this looks like a big mess...
        // @todo-ling: clean up
        //--------------------------------------------
        $productId = $model['product_id'];
        $productDetails = [];
        if (array_key_exists('productDetails', $model)) {
            $productDetails = $model['productDetails'];
        }

        $cardId = $model['card_id'];
        ApplicationRegistry::set("ekom.cardId", $cardId); // required by breadcrumbs

        Hooks::call("Ekom_onProductVisited", $productId, $productDetails);


        //--------------------------------------------
        // seo
        //--------------------------------------------
        E::seo($model['metaTitle'], $model['metaDescription'], $model['metaKeywords']);

        $isConnected = E::userIsConnected();


        //--------------------------------------------
        // features
        //--------------------------------------------
        $features = EkomApi::inst()->featureLayer()->getFeaturesModelByProductId($productId);
        $technicalDescription = "";
        if (count($features) > 0) {
            $technicalDescription = $features[0]['technical_description'];
        }


        //--------------------------------------------
        // related product cards
        //--------------------------------------------
        $relatedProducts = EkomApi::inst()->productSelectionLayer()->getProductBoxModelsByRelatedId($cardId);


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
        // related trainings
        //--------------------------------------------
        /**
         * @todo-ling: doesn't belong to ekom, do a ThisApp.ProductCardController?
         * Same for events
         */
        $relatedTrainings = EkomTrainingProductsApi::inst()->trainingProductSelectionLayer()->getTrainingProductBoxModelsByRelatedId($cardId);
        $hasRelatedTrainings = (count($relatedTrainings) > 0);
        $isTraining = ThisAppHelper::isTraining($model['seller']);
        $isEvent = ThisAppHelper::isEvent($model);


        //--------------------------------------------
        // CHOOSING WIDGETS
        //--------------------------------------------
        /**
         * @todo-ling: the mess above should be about one line per widget...
         * @todo-ling: Plus, the mess below is application specific and shouldn't be in an ekom owned controller...
         */
        $claws
            ->setWidget("maincontent.productBox", ClawsWidget::create()
                ->setTemplate("Ekom/Product/ProductBox/leaderfit")
                ->setConf($model)
            );


        if ($relatedProducts) {
            $claws
                ->setWidget("maincontent.featuredProducts", ClawsWidget::create()
                    // grid => 1
                    ->setTemplate("Ekom/CarouselProducts/default")
                    ->setConf([
                        "title" => "VOUS AIMEREZ AUSSI",
                        "products" => $relatedProducts,
                    ])
                );
        }


        $hasRelatedProducts = (count($relatedProducts) > 0);


        $productFeaturesBar = [
            'hasBundle' => $hasBundle,
            'hasRelatedProducts' => $hasRelatedProducts,
            'hasRelatedTrainings' => $hasRelatedTrainings,
            'nbComments' => $nbComments,
            'seller' => $model['seller'],
            // ekom composable features, change them if you want to disable some default widgets
            '_features' => true,
        ];

        /**
         * This particular hook has double function (we wanted to save one hook call):
         *
         * - you can decorate the productFeaturesBar model
         * - you can disable default ekom widgets by using underscore (_) prefixed variables
         *      The available names are the following:
         *          - _features: false, set this to false to prevent ekom to add the features widget
         *
         */
        Hooks::call("Ekom_decorateProductFeaturesBar_ViewConf", $productFeaturesBar, $model);


        $claws
            ->setWidget("maincontent.productFeaturesBar", ClawsWidget::create()
                ->setTemplate("Ekom/Product/ProductFeaturesBar/default")
                ->setConf($productFeaturesBar)
            );

        if (false !== $productFeaturesBar['_features']) {
            $claws
                ->setWidget("maincontent.features", ClawsWidget::create()
                    ->setTemplate("Ekom/Product/Features/default")
                    ->setConf([
                        "technical_description" => $technicalDescription,
                        "features" => $features,
                        'seller' => $model['seller'],
                    ])
                );
        }


        Hooks::call("Ekom_decorateProductBoxClaws", $claws, $model);


        if (true === $isTraining) {
            $claws
                ->setWidget("maincontent.trainers", ClawsWidget::create()
                    ->setTemplate("EkomTrainingProducts/Trainers/default")
                    ->setConf($model['trainingInfo'])
                );
        }


        if (true === $hasBundle) {
            $claws
                ->setWidget("maincontent.bundles", ClawsWidget::create()
                    ->setTemplate("Ekom/Product/Bundles/default")
                    ->setConf([
                        "product_id" => $productId,
                        "bundles" => $bundles,
                    ])
                );
        }


        $claws
            ->setWidget('maincontent.banner', ClawsWidget::create()
                ->setTemplate("Ekom/Banner/prototype4")
            )
            ->setWidget("maincontent.comments", ClawsWidget::create()
                ->setTemplate("Ekom/Product/Comments/default")
                ->setConf([
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
                ])
            )
            ->setWidget('maincontent.relatedTraining', ClawsWidget::create()
                ->setTemplate("Ekom/Product/RelatedTraining/default")
                ->setConf([
                    "relatedTrainings" => $relatedTrainings,
                ])
            );


        if ($isConnected) {
            $userId = E::getUserId();
            $lastVisited = EkomApi::inst()->productSelectionLayer()->getProductBoxModelsByLastVisited($userId);
            if ($lastVisited) {
                $claws
                    ->setWidget('maincontent.recentProducts', ClawsWidget::create()
                        // 'grid' => "1",
                        ->setTemplate("Ekom/CarouselProducts/default")
                        ->setConf([
                            "title" => "DERNIERS PRODUITS CONSULTÉS",
                            "products" => $lastVisited,
                        ])
                    );
            }
        }
    }





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