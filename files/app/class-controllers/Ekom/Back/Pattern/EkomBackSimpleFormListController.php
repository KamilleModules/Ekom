<?php


namespace Controller\Ekom\Back\Pattern;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;


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
class EkomBackSimpleFormListController extends EkomBackController
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

        if (empty($_GET)) {
            EkomNullosUser::set("nullos-morphic-persistence-back-buttons", []);
        }

        $sessionStore = EkomNullosUser::get("nullos-morphic-persistence-back-buttons", []);

        //--------------------------------------------
        //
        //--------------------------------------------
        $context = (array_key_exists("context", $this->params)) ? $this->params['context'] : [];
        $title = $this->params['title'];
        $breadcrumb = $this->params['breadcrumb'];
        $form = $this->params['form'];
        $route = (array_key_exists('route', $this->params)) ? $this->params['route'] : null;
        $list = $this->params['list'];
        $ric = $this->params['ric'];
        $newItemBtnText = null;
        if (array_key_exists("newItemBtnText", $this->params)) {
            $newItemBtnText = $this->params['newItemBtnText'];
            $newItemBtnLink = (array_key_exists("newItemBtnLink", $this->params)) ? $this->params['newItemBtnLink'] : null;
            $newItemBtnRoute = (array_key_exists("newItemBtnRoute", $this->params)) ? $this->params['newItemBtnRoute'] : null;
        }

        $buttonsList = [];
        if (array_key_exists("buttonsList", $this->params)) {
            $buttonsList = $this->params['buttonsList'];
        }


        $menuCurrentRoute = (array_key_exists("menuCurrentRoute", $this->params)) ? $this->params['menuCurrentRoute'] : null;
        $buttons = (array_key_exists("buttons", $this->params)) ? $this->params['buttons'] : [];


        $conf = [
            'listConfig' => A::getMorphicListConfig('Ekom', $list, $context),
        ];
        if ($menuCurrentRoute) {
            $conf['menuCurrentUri'] = E::link($menuCurrentRoute, [], true);
        }


        if (array_key_exists($route, $sessionStore)) {
            list($sessionLink, $sessionLabel) = $sessionStore[$route];
            $buttons[] = [
                "label" => $sessionLabel,
                "icon" => "fa fa-list",
                "link" => $sessionLink,
            ];
        }

        //--------------------------------------------
        // FORM: do we display the form
        //--------------------------------------------
        if (array_key_exists("form", $_GET)) {
//            $isUpdate = true;
//            if (is_array($ric)) {
//                foreach ($ric as $col) {
//                    if (false === array_key_exists($col, $_GET)) {
//                        $isUpdate = false;
//                        break;
//                    }
//                }
//            }
//            if (true === $isUpdate) {
//            }


            $formConfig = A::getMorphicFormConfig('Ekom', $form, $context);
            $formConfig['context'] = $context;
            $conf['formConfig'] = $formConfig;
            $this->handleMorphicForm($formConfig);
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
                    "buttonsList" => $buttonsList,
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/FormList/default')
                ->setConf($conf)
            );
    }


    protected function doRenderWithParent($elementInfo, $parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {

        $table = $elementInfo['table'];
        $ric = $elementInfo['ric'];
        $elLabel = $elementInfo['label'];
        $elLabelPlural = $elementInfo['labelPlural'];
        $elRoute = $elementInfo['route'];

        list($label, $labelPlural) = $labels;
        $k2vRef = [];
        foreach ($parentKey2Values as $k => $v) {
            $k2vRef[$parentKeys2ReferenceKeys[$k]] = $v;
        }


        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------
        $context = $parentKey2Values;
        $parentKeys = array_keys($parentKey2Values);
        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn($parentTable);
            $q = "select $repr from `$parentTable`";
            $markers = [];
            QuickPdoStmtTool::addWhereEqualsSubStmt($k2vRef, $q, $markers);
            $avatar = QuickPdo::fetch($q, $markers, \PDO::FETCH_COLUMN);
        }

        $parentKey2ValuesHttp = http_build_query($parentKey2Values);
        $parentKey2ValuesHttpParent = http_build_query($k2vRef);

//        az($parentKey2Values, $k2vRef);

        $context["avatar"] = $avatar;
        $context['_parentKeys'] = $parentKeys;


        //--------------------------------------------
        // PERSISTENCE LAYER
        //--------------------------------------------
        $sessionStore = EkomNullosUser::get("nullos-morphic-persistence-back-buttons", []);
        if (array_key_exists("s", $_GET)) {
//            $sessionLink = E::link($route) . "?form&$parentKey2ValuesHttpParent";
            $sessionLabel = "Back to $label \"$avatar\" page";
            $sessionStore[$elRoute] = [$_SERVER['HTTP_REFERER'], $sessionLabel];
            EkomNullosUser::set("nullos-morphic-persistence-back-buttons", $sessionStore);
        }


        //--------------------------------------------
        //
        //--------------------------------------------
        return $this->doRenderFormList([
            'title' => "$elLabelPlural for $label \"$avatar\"",
            'breadcrumb' => $table,
            'form' => $table,
            'list' => $table,
            'ric' => $ric,
            'route' => $elRoute,
            "newItemBtnText" => "Add a new $elLabel for $label \"$avatar\"",
            "newItemBtnLink" => E::link($elRoute) . "?form&$parentKey2ValuesHttp",
            "menuCurrentRoute" => $route,
            "buttons" => [],
            "buttonsList" => [],
            "context" => $context,
        ]);
    }


    protected function getContextFromUrl($key)
    {
        if (array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }
        throw new \Exception("variable not found in url $key");
    }
}