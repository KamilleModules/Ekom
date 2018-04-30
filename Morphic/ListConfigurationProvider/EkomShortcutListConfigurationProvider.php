<?php


namespace Module\Ekom\Morphic\ListConfigurationProvider;

use Kamille\Utils\Morphic\ListConfigurationProvider\ShortcutListConfigurationProvider;
use Module\Ekom\Morphic\MorphicConfigurationProviderHelper;

class EkomShortcutListConfigurationProvider extends ShortcutListConfigurationProvider
{
    protected function getFile($module, $identifier, array $context = [])
    {
        list($module, $identifier) = MorphicConfigurationProviderHelper::getModuleAndIdentifier($module, $identifier);

        $f = $this->confDir . "/$module/$identifier.list.conf.php";
        if (file_exists($f)) {
            return $f;
        }
        return $this->confDir . "/$module/generated/$identifier.list.conf.php";
    }
}