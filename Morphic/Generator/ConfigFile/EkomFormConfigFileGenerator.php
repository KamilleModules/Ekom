<?php


namespace Module\Ekom\Morphic\Generator\ConfigFile;


use Module\NullosAdmin\Morphic\Generator\ConfigFile\NullosFormConfigFileGenerator;

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


    protected function getPivotLinkRoute(array $operation, array $config)
    {
        $Camel = $operation['CamelCase'];
        return "NullosAdmin_Ekom_" . $Camel . "_List";
    }
}