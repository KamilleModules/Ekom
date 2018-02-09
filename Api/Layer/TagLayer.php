<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class TagLayer
{
    public static function getItems()
    {
        return QuickPdo::fetchAll("
select id, concat(id, '. ', name) as name from ek_tag order by name asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }
}