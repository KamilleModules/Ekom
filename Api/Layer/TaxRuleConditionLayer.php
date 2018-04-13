<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Bat\HashTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use SqlQuery\SqlQuery;


class TaxRuleConditionLayer
{

    public static function getTaxesIdList(int $taxRuleConditionId){
        return  QuickPdo::fetchAll("
select tax_id 
from ek_tax_rule_condition_has_tax   
where tax_rule_condition_id=$taxRuleConditionId 
order by `order` asc
", [], \PDO::FETCH_COLUMN);
    }
}