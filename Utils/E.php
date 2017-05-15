<?php


namespace Module\Ekom\Utils;


use Kamille\Utils\Routsy\LinkGenerator\ApplicationLinkGenerator;

class E
{

    public static function link($routeId, array $params = [])
    {
        return ApplicationLinkGenerator::getUri($routeId, $params);
    }

}