<?php


namespace Module\Ekom\SqlQueryWrapper;


use Module\Ekom\Api\Layer\MiniProductBoxLayer;
use SqlQueryWrapper\Plugins\SqlQueryWrapperPaginationPlugin;
use SqlQueryWrapper\Plugins\SqlQueryWrapperSortPlugin;
use SqlQueryWrapper\SqlQueryWrapper;



class EkomProductListSqlQueryWrapper extends EkomSqlQueryWrapper
{
    public static function create()
    {
        $self = parent::create();
        $self->setRowDecoratorByPreset("minibox")
            ->setPlugin("pagination", SqlQueryWrapperPaginationPlugin::create()->setNumberOfItemsPerPage(20))
            ->setPlugin("sort", SqlQueryWrapperSortPlugin::create()
                ->setDefaultSort("label_asc")
                ->setSortItems([
                "label_asc" => "Nom ascendant",
                "label_desc" => "Nom descendant",
                "price_asc" => "Prix ascendant",
                "price_desc" => "Prix descendant",
                "popularity_desc" => "Popularité",
            ]));

        return $self;
    }


}