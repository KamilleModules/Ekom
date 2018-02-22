<?php


namespace Module\Ekom\Morphic\Generator;


use Module\NullosAdmin\Morphic\Generator\NullosMorphicGenerator2;

class EkomNullosMorphicGenerator2 extends NullosMorphicGenerator2
{


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
        return '"menuCurrentRoute" => "$tableInfo[route]",';
    }


    protected function _getListConfigFileHeader(array $tableInfo)
    {
        $s = <<<EEE
<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


\$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];

EEE;

        return $s;
    }

}