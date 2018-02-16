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


    protected function getSqlQuery(array $operation, array $config, array $contextCols = [])
    {
        if ("something") {

        }
        return parent::getSqlQuery($operation, $config, $contextCols);
    }


    protected function onPhpFileReady(PhpFile $file, array $operation)
    {
        $columnTypes = $operation['columnTypes'];
        if (array_key_exists("shop_id", $columnTypes)) {
            $file->addHeadStatement(<<<EEE
\$shop_id = EkomNullosUser::getEkomValue("shop_id");
EEE
            );
            $file->addUseStatement('use Module\Ekom\Back\User\EkomNullosUser;');

        }
    }


    protected function onSqlQueryAddedAfter(PhpFile $file, array $operation)
    {
        $columnTypes = $operation['columnTypes'];
        if (array_key_exists("shop_id", $columnTypes)) {
            $file->addBodyStatement(<<<EEE
\$q .= ' where shop_id=' . \$shop_id;
EEE
            );

        }
    }

}