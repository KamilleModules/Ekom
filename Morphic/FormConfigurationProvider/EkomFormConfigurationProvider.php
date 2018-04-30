<?php


namespace Module\Ekom\Morphic\FormConfigurationProvider;

use Kamille\Utils\Morphic\FormConfigurationProvider\FormConfigurationProvider;
use Module\Ekom\Morphic\MorphicConfigurationProviderHelper;

class EkomFormConfigurationProvider extends FormConfigurationProvider
{


    protected function getFile($module, $identifier, array $context = [])
    {

        list($module, $identifier) = MorphicConfigurationProviderHelper::getModuleAndIdentifier($module, $identifier);


        $f = $this->confDir . "/$module/$identifier.form.conf.php";
        if (file_exists($f)) {
            return $f;
        }
        return $this->confDir . "/$module/generated/$identifier.form.conf.php";
    }
}