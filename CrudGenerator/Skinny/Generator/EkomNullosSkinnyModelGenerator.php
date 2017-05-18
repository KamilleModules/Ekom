<?php


namespace Module\Ekom\CrudGenerator\Skinny\Generator;


use Bat\FileSystemTool;
use CrudGeneratorTools\Skinny\Generator\SkinnyModelGenerator;
use CrudGeneratorTools\Skinny\Helper\SkinnyHelper;
use CrudGeneratorTools\Util\ForeignKeyPreferredColumnUtil;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\AutoAdmin\CrudGenerator\Skinny\Generator\NullosSkinnyModelGenerator;
use Module\AutoAdmin\CrudGenerator\Util\NullosForeignKeyPreferredColumnUtil;
use QuickPdo\Util\QuickPdoInfoCacheUtil;

class EkomNullosSkinnyModelGenerator extends NullosSkinnyModelGenerator
{
    protected function getTargetRelativeDir($profileId, $db, $table, $column)
    {
        return "www/img/products";
    }
}