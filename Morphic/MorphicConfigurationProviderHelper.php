<?php


namespace Module\Ekom\Morphic;


class MorphicConfigurationProviderHelper
{


    public static function getModuleAndIdentifier(string $module, string $identifier)
    {
        if (false !== strpos($identifier, ":")) {
            $p = explode(":", $identifier);
            /**
             * This is a way to override the module just using an identifier,
             * that's because sometimes, modules just have an identifier to express themselves,
             * and it turns out that the current mechanism of stating the module name as a parameter
             * was just not enough to handle the various use cases I had to deal with.
             * If I was to redo the system, I would drop the module parameter and rely only on the identifier.
             * (but now it's too late, the implementation has already begun and other systems have dependencies
             * on the old mechanism...)
             */
            $module = $p[0];
            $identifier = $p[1];
        }
        return [$module, $identifier];
    }

}