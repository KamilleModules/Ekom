<?php


namespace Module\Ekom\Morphic\ListConfigurationProvider;

use Kamille\Utils\Morphic\ListConfigurationProvider\ShortcutListConfigurationProvider;

class EkomShortcutListConfigurationProvider extends ShortcutListConfigurationProvider
{
    protected function getFile($module, $identifier, array $context = [])
    {
        $f = $this->confDir . "/$module/$identifier.list.conf.php";
        if (file_exists($f)) {
            return $f;
        }
        return $this->confDir . "/$module/generated/$identifier.list.conf.php";
    }
}