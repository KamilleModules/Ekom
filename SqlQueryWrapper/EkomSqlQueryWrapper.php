<?php


namespace Module\Ekom\SqlQueryWrapper;


use Module\Ekom\Api\Layer\MiniProductBoxLayer;
use SqlQueryWrapper\SqlQueryWrapper;

class EkomSqlQueryWrapper extends SqlQueryWrapper
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