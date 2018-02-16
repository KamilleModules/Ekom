<?php


namespace Module\Ekom\Morphic\FormConfigurationProvider;

use Kamille\Utils\Morphic\FormConfigurationProvider\FormConfigurationProvider;

class EkomFormConfigurationProvider extends FormConfigurationProvider
{


    protected function getFile($module, $identifier, array $context = [])
    {
        $f = $this->confDir . "/$module/$identifier.form.conf.php";
        if (file_exists($f)) {
            return $f;
        }
        return $this->confDir . "/$module/generated/$identifier.form.conf.php";
    }
}