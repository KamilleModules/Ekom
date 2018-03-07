<?php


namespace Controller\Ekom\Back\Pattern;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;


/**
 * Helps creating a simple list/form pattern.
 *
 * The list appears alone.
 * There is a btn to add a new item.
 *
 * If the user clicks that button, the insert form appears above the list.
 * If the user clicks the update btn of an item in the list, the update form appears above the list.
 *
 *
 */
class EkomBackSimpleFormListControllerBack extends EkomBackController
{


    private $params;

    public function __construct()
    {
        parent::__construct();
        $this->params = null;
    }


    protected function doRenderFormList(array $params)
    {
        $this->params = $params;
        return $this->renderClaws();
    }


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $context = (array_key_exists("context", $this->params)) ? $this->params['context'] : [];
        $title = $this->params['title'];
        $breadcrumb = $this->params['breadcrumb'];
        $form = $this->params['form'];
        $list = $this->params['list'];
        $ric = $this->params['ric'];
        $newItemBtnText = $this->params['newItemBtnText'];

        $newItemBtnLink = (array_key_exists("newItemBtnLink", $this->params)) ? $this->params['newItemBtnLink'] : null;
        $newItemBtnRoute = (array_key_exists("newItemBtnRoute", $this->params)) ? $this->params['newItemBtnRoute'] : null;
        $menuCurrentRoute = (array_key_exists("menuCurrentRoute", $this->params)) ? $this->params['menuCurrentRoute'] : null;
        $buttons = (array_key_exists("buttons", $this->params)) ? $this->params['buttons'] : [];


        $conf = [
            'listConfig' => A::getMorphicListConfig('Ekom', $list, $context),
        ];
        if ($menuCurrentRoute) {
            $conf['menuCurrentUri'] = E::link($menuCurrentRoute, [], true);
        }

        if (
            array_key_exists("form", $_GET) ||
            (is_string($ric) && array_key_exists($ric, $_GET)) ||
            is_array($ric)
        ) {
            $ok = true;
            if (is_array($ric) && false === array_key_exists("form", $_GET)) {
                foreach ($ric as $col) {
                    if (false === array_key_exists($col, $_GET)) {
                        $ok = false;
                        break;
                    }
                }
            }
            if (true === $ok) {
                $formConfig = A::getMorphicFormConfig('Ekom', $form, $context);
                $formConfig['context'] = $context;
                $conf['formConfig'] = $formConfig;
                $this->handleMorphicForm($formConfig);
            }
        }


        if ($newItemBtnText) {
            if ($newItemBtnLink) {

                $buttons[] = [
                    "label" => $newItemBtnText,
                    "icon" => "fa fa-plus-circle",
                    "link" => $newItemBtnLink,
                ];
            } else {
                $buttons[] = [
                    "label" => $newItemBtnText,
                    "icon" => "fa fa-plus-circle",
                    "link" => E::link($newItemBtnRoute) . "?form",
                ];
            }
        }

        $this->getClaws()
            //--------------------------------------------
            // MAIN
            //--------------------------------------------
            ->setWidget("maincontent.pageTop", ClawsWidget::create()
                ->setTemplate('Ekom/Main/PageTop/default')
                ->setConf([
                    "breadcrumbs" => BreadcrumbsHelper::getBreadCrumbsModel([
                        $breadcrumb,
                    ]),
                    "title" => $title,
                    "buttons" => $buttons,
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/FormList/default')
                ->setConf($conf)
            );
    }


    protected function getContextFromUrl($key)
    {
        if (array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }
        throw new \Exception("variable not found in url $key");
    }

}