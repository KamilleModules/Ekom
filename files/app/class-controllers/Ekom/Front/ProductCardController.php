<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Model\Front\ProductPageModel;
use Module\Ekom\Utils\E;

class ProductCardController extends EkomFrontController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();
        $ref = Z::getUrlParam('ref'); // product reference
        $model = ProductPageModel::getModelByProductReference($ref);
        $this->prepareByPageModel($model);
    }


    public function renderClawsByProductId()
    {
        parent::prepareClaws();
        $productId = Z::getUrlParam('id'); // product id
        $model = ProductPageModel::getModelByProductId($productId);
//        az(__FILE__, $model);
        $this->prepareByPageModel($model);
        return $this->doRenderClaws();
    }


    //--------------------------------------------
    // override me
    //--------------------------------------------
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


    protected function prepareHead(array $model)
    {
        $this->getClaws()
            ->setWidget("maincontent.productBox", ClawsWidget::create()
                ->setTemplate("Ekom/Product/ProductBox/leaderfit")
                ->setConf($model)
            );
    }

    protected function prepareTail(array $tailModel, array $headModel)
    {

        $features = $tailModel['features'];
        $commentsModel = $tailModel['comments'];
        $featureBar = $tailModel['featureBar'];
        $claws = $this->getClaws();


        $this->getClaws()
            ->setWidget("maincontent.productFeaturesBar", ClawsWidget::create()
                ->setTemplate("Ekom/Product/ProductFeaturesBar/default")
                ->setConf($featureBar)
            );

        /**
         * @todo-ling: understand why this is related (features and featureBar)
         */
        if (false !== $featureBar['_features']) {
            $claws
                ->setWidget("maincontent.features", ClawsWidget::create()
                    ->setTemplate("Ekom/Product/Features/default")
                    ->setConf($features)
                );
        }


        $claws
            ->setWidget('maincontent.banner', ClawsWidget::create()
                ->setTemplate("Ekom/Banner/prototype4")
            )
            ->setWidget("maincontent.comments", ClawsWidget::create()
                ->setTemplate("Ekom/Product/Comments/default")
                ->setConf($commentsModel)
            );


        Hooks::call("Ekom_ProductCard_prepareClaws", $claws, $headModel, $tailModel);

    }
    //--------------------------------------------
    //
    //--------------------------------------------
    private function prepareByPageModel(array $pageModel)
    {
        HtmlPageHelper::addBodyClass("product-page");
        $claws = $this->getClaws();
        $head = $pageModel['head']; // product box model
        $tail = $pageModel['tail']; // product page tail model


        //--------------------------------------------
        // CHOOSING WIDGETS
        //--------------------------------------------
        if (array_key_exists("errorCode", $head)) {
            $claws->setLayout("sandwich_1c/default");
            $this->productError($head);

        } else {

            //--------------------------------------------
            // APPLICATION - INTERACTION
            //--------------------------------------------
//            $productDetails = $head['productDetailsMap'];
//            $productId = $head['product_id'];
//            Hooks::call("Ekom_onProductVisited", $productId, $productDetails);



            $cardId = $head['card_id'];
            ApplicationRegistry::set("ekom.cardId", $cardId); // required by breadcrumbs

            E::seo($head['metaTitle'], $head['metaDescription'], $head['metaKeywords']);
            $claws->setLayout("sandwich_1c/default");
            $this->prepareHead($head);
            $this->prepareTail($tail, $head);
        }
    }
}