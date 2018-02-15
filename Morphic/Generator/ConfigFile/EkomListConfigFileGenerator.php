<?php


namespace Module\Ekom\Morphic\Generator\ConfigFile;


use Bat\CaseTool;
use Module\NullosAdmin\Morphic\Generator\ConfigFile\NullosFormConfigFileGenerator;
use Module\NullosAdmin\Morphic\Generator\ConfigFile\NullosListConfigFileGenerator;
use PhpFile\PhpFile;
use QuickPdo\QuickPdo;

class EkomListConfigFileGenerator extends NullosListConfigFileGenerator
{

    protected function prepareHeaderCols(array &$columns)
    {

        if ("notAdmin") {
            unset($columns["shop_id"]);
        }
    }

    protected function prepareQueryCols(array &$columns)
    {

        if ("notAdmin") {
            unset($columns["shop_id"]);
        }
    }


    protected function getSqlQuery(array $operation, array $config, array $contextCols=[])
    {
        if ("something") {

        }
        return parent::getSqlQuery($operation, $config, $contextCols);
    }
}