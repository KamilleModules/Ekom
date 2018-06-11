<?php


namespace Module\Ekom\SqlQueryWrapper;


use Module\Application\SqlQueryWrapper\ApplicationSqlQueryWrapper;
use Module\Ekom\Api\Layer\MiniProductBoxLayer;

class EkomSqlQueryWrapper extends ApplicationSqlQueryWrapper
{

    public function setRowDecoratorByPreset(string $presetName)
    {
        switch ($presetName) {
            case "minibox":
                $this->setRowDecorator(function (array &$row) {
                    MiniProductBoxLayer::sugarify($row);
                });
                break;
            default:
                break;
        }
        return $this;
    }
}