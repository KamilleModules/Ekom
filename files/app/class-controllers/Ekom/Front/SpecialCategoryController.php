<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Ling\Z;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\EkomEvents\Api\EkomEventsApi;
use Module\EkomTrainingProducts\Api\EkomTrainingProductsApi;
use Module\ThisApp\Api\Layer\CategoryGridLayer;
use Module\ThisApp\Api\ThisAppApi;

class SpecialCategoryController extends EkomFrontController
{

    public function prepareClaws()
    {
        parent::prepareClaws();


        /**
         * @todo-ling: see files like config/laws/themes/lee/Ekom/category-events.conf.php
         */
        $category = Z::getUrlParam("category");
        switch ($category) {
            case "equipement":
                $this->prepareEquipementCategory();
                break;
            case "events":
                $this->prepareEventsCategory();
                break;
            case "formation":
                $this->prepareFormationCategory();
                break;
            default:
                throw new \Exception("Not implemented yet: with category=$category");
                break;
        }


    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function prepareEquipementCategory()
    {
        $conf = CategoryGridLayer::getEquipementGridModel();

        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.category", ClawsWidget::create()
                ->setTemplate("Ekom/EventsCategoryGrid/default")
                ->setConf($conf)
            )
            ->setWidget("maincontent.seoText", ClawsWidget::create()
                ->setTemplate("Ekom/SeoText/default")
                ->setConf([
                    "text" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus animi, architecto at consectetur cumque facilis fuga ipsam iusto maiores nemo nisi, perferendis quis quisquam quod totam? Dolorum fugit itaque voluptatem.",
                ])
            )
            ->setWidget("maincontent.twoBanners", ClawsWidget::create()
                ->setTemplate("Ekom/TwoBanners/prototype4")
            )
            ->setWidget("maincontent.banner", ClawsWidget::create()
                ->setTemplate("Ekom/Banner/prototype3")
            );
    }


    protected function prepareEventsCategory()
    {
        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.category", ClawsWidget::create()
                ->setTemplate("Ekom/EventsCategoryGrid/default")
                ->setConf($this->getCatsModelByType('events'))
            )
            ->setWidget("maincontent.seoText", ClawsWidget::create()
                ->setTemplate("Ekom/SeoText/default")
                ->setConf([
                    "text" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus animi, architecto at consectetur cumque facilis fuga ipsam iusto maiores nemo nisi, perferendis quis quisquam quod totam? Dolorum fugit itaque voluptatem.",
                ])
            )
            ->setWidget("maincontent.searchModule", ClawsWidget::create()
                ->setTemplate("Ekom/EventsSearchModule/default")
                ->setConf(EkomEventsApi::inst()->searchEventLayer()->getEventsSearchModel())
            )
            ->setWidget("maincontent.twoBanners", ClawsWidget::create()
                ->setTemplate("Ekom/TwoBanners/prototype4")
            )
            ->setWidget("maincontent.banner", ClawsWidget::create()
                ->setTemplate("Ekom/Banner/prototype3")
            );
    }


    protected function prepareFormationCategory()
    {

        $conf = CategoryGridLayer::getFormationGridModel();



        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.searchModule", ClawsWidget::create()
                ->setTemplate("Ekom/FormationSearchModule/default")
                ->setConf(EkomTrainingProductsApi::inst()->searchTrainingLayer()->getTrainingSearchModel($_GET))
            )
            ->setWidget("maincontent.category", ClawsWidget::create()
                ->setTemplate("Ekom/FormationCategoryGrid/default")
                ->setConf($conf)
//                ->setConf($this->getCatsModelByType('formation'))
            )
            ->setWidget("maincontent.seoText", ClawsWidget::create()
                ->setTemplate("Ekom/SeoText/default")
                ->setConf([
                    "text" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus animi, architecto at consectetur cumque facilis fuga ipsam iusto maiores nemo nisi, perferendis quis quisquam quod totam? Dolorum fugit itaque voluptatem.",
                ])
            )
            ->setWidget("maincontent.twoBanners", ClawsWidget::create()
                ->setTemplate("Ekom/TwoBanners/prototype4")
            )
            ->setWidget("maincontent.banner", ClawsWidget::create()
                ->setTemplate("Ekom/Banner/prototype3")
            );
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getCatsModelByType($type)
    {
        $uriImg = E::getImgBaseUri();
        $cats = EkomApi::inst()->categoryLayer()->getSubCategoriesByName($type, 0);
        $tCats = [];
        foreach ($cats as $cat) {
            $cat['imgUri'] = $uriImg . "/category/" . $cat['name'] . ".jpg";
            $tCats[] = $cat;
        }

        return [
            "cats" => $tCats,
        ];
    }
}


