<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;

class SearchResultsController extends EkomFrontController
{
    protected function prepareClaws()
    {
        parent::prepareClaws();



        $model = EkomApi::inst()->searchResultsLayer()->getModel();


        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.searchResults", ClawsWidget::create()
                ->setTemplate("Ekom/SearchResults/default")
                ->setConf($model)
            );
    }
}


