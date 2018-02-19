<?php


namespace Module\Ekom\Morphic\Generator\ConfigFile;


use Bat\CaseTool;
use Module\NullosAdmin\Morphic\Generator\ConfigFile\NullosFormConfigFileGenerator;
use PhpFile\PhpFile;
use QuickPdo\QuickPdo;

class EkomFormConfigFileGenerator extends NullosFormConfigFileGenerator
{

    protected function getOnProcessBefore(array $operation, array $config = [])
    {
        $cols = $operation['columns'];
        $hasShop = (in_array("shop_id", $cols));

        if (true === $hasShop) {
            return <<<EEE
            
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        \$fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    
EEE;
        } else {
            return "";
        }
    }


    protected function getPivotLinkRoute($table, array $dbPrefixes)
    {
//        foreach ($dbPrefixes as $prefix) {
//            if (0 === strpos($table, $prefix)) {
//                $table = substr($table, strlen($prefix));
//                break;
//            }
//        }
        $Camel = CaseTool::snakeToFlexiblePascal($table);
        return "NullosAdmin_Ekom_Generated_" . $Camel . "_List";
    }


    protected function getPivotLinkLabels($table, array $operation, array $dbPrefixes)
    {
        return $this->getPivotLinkLabelsByPrefix($dbPrefixes, $table, $operation);
    }



    //--------------------------------------------
    // CONTROL SPECIFIC
    //--------------------------------------------
    protected function doPrepareColumnControl(PhpFile $file, $params, array $config)
    {
        $type = $params['type'];
        $col = $params['column'];
        $label = $params['label'];


        switch ($type) {
            case "date":



                $file->addBodyStatement(<<<EEE
        ->addControl(EkomSokoDateControl::create()
            ->setName("$col")
            ->setLabel('$label')
        )
EEE
                );
                return true;
                break;
        }


        return false;
    }


    protected function doPrepareForeignKeyControl(PhpFile $file, $params, array $config)
    {


        $col = $params['column'];
        $label = $params['label'];
        $isInRic = $params['isInRic'];


        if ('shop_id' !== $col) {


            $autocomplete2ServiceId = [];
            if (array_key_exists("autoComplete", $config)) {
                $autocomplete2ServiceId = $config['autoComplete'];
            }


            //--------------------------------------------
            // auto complete
            //--------------------------------------------
            if (array_key_exists($col, $autocomplete2ServiceId)) {

                $service = $autocomplete2ServiceId[$col];

                $sValue = '';
                if ($isInRic) {
                    $sValue = '
->setValue($' . $col . ')                
                ';
                }


                $file->addBodyStatement(<<<EEE
        ->addControl(SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "$service",
            ]))
            ->setName("$col")
            ->setLabel('$label')
            ->setProperties([
                'readonly' => \$isUpdate,
            ])
            $sValue
        )
EEE
                );
            }
            //--------------------------------------------
            // regular foreign key
            //--------------------------------------------
            else {
                parent::doPrepareForeignKeyControl($file, $params, $config);
            }
        }
    }

}