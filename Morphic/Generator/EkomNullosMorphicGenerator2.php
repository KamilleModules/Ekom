<?php


namespace Module\Ekom\Morphic\Generator;


use Module\Ekom\Back\Helper\BackHooksHelper;
use Module\NullosAdmin\Morphic\Generator\NullosMorphicGenerator2;
use PhpFile\PhpFile;

class EkomNullosMorphicGenerator2 extends NullosMorphicGenerator2
{
    public function generate()
    {
        parent::generate();
        $this->onGenerateAfter();
    }


    protected function onGenerateAfter() // override me
    {
        $generatedItemFile = BackHooksHelper::getGeneratedMenuLocation();
        $generatedRouteFile = BackHooksHelper::getGeneratedRoutesLocation();
        $menu = PhpFile::create();
        $route = PhpFile::create();
        $menu->addUseStatement(<<<EEE
use Models\AdminSidebarMenu\Lee\Objects\Item;
use Module\NullosAdmin\Utils\N;
EEE
        );
        $menu->addBodyStatement('$generatedItem');


        foreach ($this->db2TableInfo as $db => $tableInfos) {
            foreach ($tableInfos as $tableInfo) {


                if (false !== $tableInfo['ai']) {
                    /**
                     * Note: for the label, I prefer the elementTable instead of the elementLabelPlural,
                     * because with multiple modules it makes it easier to spot the module
                     */
                    //--------------------------------------------
                    // CREATE MENU
                    //--------------------------------------------
                    $menu->addBodyStatement(<<<EEE
    ->addItem(Item::create()
        ->setActive(true)
        ->setName("$tableInfo[table]")
        ->setLabel("$tableInfo[table]")
        ->setIcon("")
        ->setLink(N::link("$tableInfo[route]"))
    )
EEE
                    );
                }


                //--------------------------------------------
                // CREATE ROUTES
                //--------------------------------------------
                $path = 'Controller\Ekom\Back\\Generated\\' . $tableInfo['camel'] . '\\' . $tableInfo['camel'] . 'ListController';
                $route->addBodyStatement(<<<EEE
\$routes["$tableInfo[route]"] = ["/ekom/generated/$tableInfo[table]/list", null, null, "$path:render"];
EEE
                );
            }
        }
        $menu->addBodyStatement(';');
        $menu->render($generatedItemFile);
        $route->render($generatedRouteFile);
    }


    protected function _getControllerClassHeader(array $tableInfo)
    {


        $s = <<<EEE
<?php

namespace Controller\Ekom\Back\Generated\\$tableInfo[camel];

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class $tableInfo[camel]ListController extends EkomBackSimpleFormListController
{

EEE;

        return $s;

    }

    protected function _getControllerRenderWithNoParentMethodExtraVar(array $tableInfo)
    {
        return '"menuCurrentRoute" => "' . $tableInfo['route'] . '",';
    }


    protected function _getListConfigFileHeader(array $tableInfo)
    {
        $s = <<<EEE
<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;

EEE;
//        $s = <<<EEE
//<?php
//
//use Module\Ekom\Back\User\EkomNullosUser;
//use Kamille\Utils\Morphic\Helper\MorphicHelper;
//
//
//\$inferred = [
//    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
//    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
//    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
//];
//
//EEE;

        return $s;
    }

    protected function _getFormInferred(array $tableInfo)
    {
        return [];
//        return [
//            'shop_id',
//            'lang_id',
//            'currency_id',
//        ];
    }


    protected function _getFormConfigFileTop(array $tableInfo, array $inferred)
    {
        $s = <<<EEE
<?php 

use Bat\SessionTool;         
use QuickPdo\QuickPdo;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use SokoForm\Form\SokoFormInterface;
use SokoForm\Form\SokoForm;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use Module\Ekom\Utils\E;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\SokoForm\Control\EkomSokoDateControl;


EEE;

        if ($inferred) {
            $s .= '// inferred data (can be overridden by fkeys)' . PHP_EOL;
            foreach ($inferred as $col) {
                $s .= '$' . $col . ' = EkomNullosUser::getEkomValue("' . $col . '");' . PHP_EOL;
            }
        }
        return $s;
    }


    protected function getAutocompleteControlContent($column)
    {
        if ('_id' === substr($column, -3)) {
            $column = substr($column, 0, -3);
        }
        return <<<EEE
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.$column",
            ]))         
EEE;
    }

    protected function doPrepareColumnControl(&$s, array $params, array $tableInfo)
    {
        $type = $params['type'];
        $col = $params['column'];
        $label = $params['label'];


        switch ($type) {
            case "date":


                $s .= PHP_EOL . <<<EEE
        ->addControl(EkomSokoDateControl::create()
            ->setName("$col")
            ->setLabel('$label')
        )
EEE;
                return true;
                break;
        }

        return false;
    }

    protected function getTableRouteByTable($table)
    {
        $camel = $this->getCamelByTable($table);
        return "Ekom_Back_Generated_" . $camel . "_List";
    }


    protected function getForeignKeyExtraLink($fkType, $col, $label, $route, array $tableInfo, array $fkTableInfo)
    {
        if ('ai' !== $fkType) {

            $rks = $tableInfo['rks'];


            $reversedFields = [];
            foreach ($rks as $info) {
                if ($info[1] === $fkTableInfo['table']) {
                    $reversedFields = $info[2];
                    break;
                }
            }


            $linkArgs = '';
            if ($reversedFields) {
                foreach ($reversedFields as $k => $v) {
                    $linkArgs .= "&$v=' . \$$k . '";
                }
            }


            return "
                    'extraLink' => [
                        'text' => 'Créer un nouvel élément \"$label\"',
                        'icon' => 'fa fa-plus',
                        'link' => E::link('$route') . '?form$linkArgs',
                    ],";
        }
        return "";
    }
}