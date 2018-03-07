<?php


namespace Controller\Ekom\Back\Country;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class CountryTranslationFormController extends EkomBackController
{

    protected function prepareClaws()
    {

        $config = A::getMorphicFormConfig('Ekom', 'country_translation');
        $this->handleMorphicForm($config);



        parent::prepareClaws();



        $this->getClaws()
            //--------------------------------------------
            // MAIN
            //--------------------------------------------
            ->setWidget("maincontent.pageTop", ClawsWidget::create()
                ->setTemplate('Ekom/Main/PageTop/default')
                ->setConf([
                    "breadcrumbs" => BreadcrumbsHelper::getBreadCrumbsModel([
                        'admin',
                        'country',
                    ]),
                    "title" => "Country translation",
                    "buttons" => [
                        [
                            "label" => "List of country translations",
                            "icon" => "fa fa-list",
                            "link" => E::link("NullosAdmin_Ekom_CountryTranslation_List"),
                        ],
                    ],
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/Form/default')
                ->setConf([
                    'formConfig' => $config,
                ])
            );
    }

}