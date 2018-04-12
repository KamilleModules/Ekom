<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class GenderLayer
{


    public static function getListItems()
    {
        return QuickPdo::fetchAll('
select id, label
from ek_gender
', [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }
}