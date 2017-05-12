<?php


namespace Module\Ekom\Utils;


use Kamille\Utils\Routsy\LinkGenerator\ApplicationLinkGenerator;

class E
{

    public static function link($route, array $params = [])
    {
        return ApplicationLinkGenerator::getUri($route, $params);
    }

}