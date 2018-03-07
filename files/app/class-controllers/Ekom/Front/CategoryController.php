<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Model\Front\DynamicProductListModel;
use Module\Ekom\Utils\E;
use Module\Ekom\WidgetBrain\Front\CategorySummary;


class CategoryController extends EkomFrontController
{


    protected function prepareClawsEvents()
    {
        parent::prepareClaws();

        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.featuredEEvent", ClawsWidget::create()
                ->setTemplate("ThisApp/Events/FeaturedEvents/default")
                ->setConf([
                    "uri" => "/theme/lee/assets/events/zenidays.jpg",
                    "alt" => "Zenidays",
                ])
            );
    }

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $slug = Z::getUrlParam("slug");

        if ('events' === $slug) {
            $this->prepareClawsEvents();
        } else {


            $model = DynamicProductListModel::getModelByCategorySlug($slug);
            $categoryId = $model['category_id'];

//        az($model);
            // DynamicProductListModel already sets ekom.categoryId via the ApplicationRegistry
            // this is required by the breadcrumbs and categorySummary widgets.
            // as for now, this workaround does the job, but the real question remains:
            // should DynamicProductListModel mess with ApplicationRegistry::set(ekom.categoryId) ?
            ApplicationRegistry::set("ekom.categoryId", $categoryId);


            $this->getClaws()
                ->setLayout("sandwich_2c/product-list")
                ->setWidget("listheader.categorySummary", ClawsWidget::create()
                    ->setTemplate("Ekom/CategorySummary/default")
                    ->setConf(CategorySummary::create()->getModel($categoryId))
                )
                ->setWidget("maincontent.productList", ClawsWidget::create()
                    ->setTemplate("Ekom/ProductList/ProductCardList/hybrid")
                    ->setConf([
                        'listBundle' => $model['bundle'],
                    ])
                )
                ->setWidget("sidebar.summaryFilters", ClawsWidget::create()
                    ->setTemplate("Ekom/ListFilter/Summary/leaderfit")
                    ->setConf($model['bundle']['filters']['summary']), 'first'
                )
                ->setWidget("sidebar.priceFilter", ClawsWidget::create()
                    ->setTemplate("Ekom/ListFilter/Price/hybrid")
                    ->setConf($model['bundle']['filters']['price']), "after:sidebar.summaryFilters"
                )
                ->setWidget("sidebar.attributeFilters", ClawsWidget::create()
                    ->setTemplate("Ekom/ListFilter/Attributes/default")
                    ->setConf([
                        'filterBoxes' => $model['bundle']['filters']['attributes'],
                    ])
                )
                ->setWidget("sidebar.discountFilter", ClawsWidget::create()
                    ->setTemplate("Ekom/ListFilter/Discount/default")
                    ->setConf($model['bundle']['filters']['discounts']),
                    "after:sidebar.priceFilter"
                );


            $claws = $this->getClaws();
            Hooks::call("Ekom_CategoryController_decorateClaws", $claws, $model);


//        $userId = E::getUserId(null);
//        if ($userId) {
//            $historyItems = EkomUserProductHistoryApi::inst()->generalLayer()->getHistoryItemsSlice($userId);
//            $claws->setWidget("listfooter.userLastVisitedProducts", ClawsWidget::create()
//                ->setTemplate("Ekom/CarouselProducts/default")
//                ->setConf([
//                    'title' => "DERNIERS PRODUITS CONSULTÉS",
//                    'products' => $historyItems,
//                ])
//            );
//        }
        }
    }
}


