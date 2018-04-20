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


/**
 * Models used by this class:
 *
 *
 * ------------------------------
 * taxItem:
 *      - label: translated label of the tax
 *      - amount: in percent
 *      - order: order of this tax in the tax group
 *      - mode: how to combine this tax with the other taxes in the group
 *      - group_label: the bo label for the tax group owning this tax
 *      - group_name: the name for the tax group owning this tax
 *
 *
 *
 * ------------------------------
 */
class TaxLayer
{


    public static function getTaxDetailsInfoByTaxRuleConditionId(int $taxRuleConditionId, $basePrice)
    {
        $rows = QuickPdo::fetchAll("
select         
t.amount as `value`, 
t.label,
h.mode

from ek_tax t 
inner join ek_tax_rule_condition_has_tax h on h.tax_id=t.id

where h.tax_rule_condition_id=$taxRuleConditionId

order by h.order asc


        ");


        $basePriceReference = $basePrice;
        foreach($rows as $k => $row){
            $basePrice = $basePrice + ($basePrice * $row['value']) / 100;
            $amount = E::trimPrice($basePrice - $basePriceReference); // the amount inferred to the price by this specific tax
            $basePriceReference = $basePrice;
            $rows[$k]['amount'] = $amount;
        }

        return $rows;

    }


    /**
     * @param $basePrice
     * @param array $taxDetails , array of items, each of which:
     *      - value
     *      - label
     *      - model
     * (see method getTaxDetailsInfoByTaxRuleConditionId of this class)
     */
    public static function decorateTaxDistribution(array &$distribution, $basePrice, array $taxDetails = [])
    {
        $newBasePrice = $basePrice;
        foreach ($taxDetails as $k => $row) {
            $value = $row['value'];
            $label = $row['label'];
            $mode = $row['mode'];
            /**
             * @todo-ling: combine the taxes depending on the mode,
             * as for now this is a virtual case so I just go one after the other....
             */
            $basePrice = $basePrice + ($basePrice * $value) / 100;
            $amount = $basePrice - $newBasePrice; // the amount inferred to the price by this specific tax
            $newBasePrice = $basePrice;

            if (false === array_key_exists($label, $distribution)) {
                $distribution[$label] = [
                    "tax_value" => $value,
                    "amount" => $amount,
                    "amount_formatted" => E::price($amount),
                ];
            } else {
                $newAmount = $distribution[$label]['amount'] + $amount;
                $distribution[$label]['amount'] = $newAmount;
                $distribution[$label]['amount_formatted'] = E::price($newAmount);
            }
        }

        return $distribution;
    }


    public static function getListItems()
    {
        return QuickPdo::fetchAll("
select id, label from ek_tax order by id asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getTaxAmountById($taxId)
    {
        $taxId = (int)$taxId;
        return QuickPdo::fetch("
select amount from ek_tax where id=$taxId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getModeItems()
    {
        return [
            "" => "(Default)",
            "merge" => "Merge",
            "chain" => "Chain",
        ];
    }

    public static function getGroupLabelByGroupId($taxGroupId)
    {
        $taxGroupId = (int)$taxGroupId;
        return QuickPdo::fetch("
select label from ek_tax_group where id=$taxGroupId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getTaxItems()
    {
        return QuickPdo::fetchAll("
select id, amount from ek_tax order by id asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    /**
     *
     * Return the taxGroup model that applies to the given cardId, or false array if the card doesnt' have any
     * tax applied to it.
     *
     * @see EkomModels::taxGroup()
     *
     */
    public static function getTaxGroupModelByTaxRuleId($taxRuleId, array $taxContext)
    {
        if (null === $taxRuleId) {
            return false;
        }
        $taxRuleId = (int)$taxRuleId;

        $hash = HashTool::getHashByArray($taxContext);
        return A::cache()->get("Ekom.TaxLayer.getTaxGroupModelByTaxRuleId-$taxRuleId-$hash", function () use ($taxRuleId, $taxContext) {

            // first get the matching condition if any
            $q = "
select 
c.id as condition_id,
c.ratio,
r.id as rule_id,
r.label as rule_label

from ek_tax_rule_condition c 
inner join ek_tax_rule r on r.id=c.tax_rule_id
 
            ";


            $markers = [];
            self::decorateQueryWithRuleConditionByTaxContext($q, $markers, $taxContext);

            $row = QuickPdo::fetch($q, $markers);
            if ($row) {
                $taxes = self::getTaxesByTaxRuleConditionId($row['condition_id']);

                return [
                    "rule_id" => $row['rule_id'],
                    "rule_label" => $row['rule_label'],
                    "ratio" => $row['ratio'],
                    "taxes" => $taxes,
                ];
            } else {
                return false;
            }
        });
    }


    private static function getTaxesByTaxRuleConditionId(int $conditionId)
    {
        return QuickPdo::fetchAll("
select 
id, 
label, 
amount,
mode, 
order 
from ek_tax t 
inner join ek_tax_rule_condition_has_tax h on h.tax_id=t.id 
where h.tax_rule_condition=$conditionId
        ");
    }


    /**
     * @param array|false $taxGroup
     * @see EkomModels::taxGroup()
     * @param $price , float, the price to apply the taxGroup to
     *
     * @return array
     */
    public static function applyTaxGroup($taxGroup, $price)
    {
        if (is_array($taxGroup) && count($taxGroup['taxes']) > 0) {
            $groupName = $taxGroup['name'];
            $groupLabel = $taxGroup['label'];
            $groupTaxes = $taxGroup['taxes'];

            $taxLayer = EkomApi::inst()->taxLayer();
            $taxDetails = [];
            $_priceWithTax = $taxLayer->applyTaxesToPrice($groupTaxes, $price, $taxDetails);
            $_priceWithoutTax = $price;
            if (false === empty($price)) {
                $taxRatio = $_priceWithTax / $price;
            } else {
                $taxRatio = 1;
                XLog::error("[Ekom module] - TaxLayer: division by zero with taxes: " . ArrayToStringTool::toPhpArray($groupTaxes));
            }
            $taxAmountUnit = $_priceWithTax - $_priceWithoutTax;

        } else {
            $taxDetails = [];
            $taxRatio = 1;
            $_priceWithoutTax = $price;
            $_priceWithTax = $price;
            $groupName = '';
            $groupLabel = '';
            $taxAmountUnit = 0;
        }


        return [
            'taxDetails' => $taxDetails,
            'taxRatio' => $taxRatio,
            'taxGroupName' => $groupName,
            'taxGroupLabel' => $groupLabel,
            'taxAmountUnit' => $taxAmountUnit,
            'priceWithoutTax' => $_priceWithoutTax,
            'priceWithTax' => $_priceWithTax,
        ];
    }

    /**
     * @todo-ling: remove that crap
     */
    public static function getTaxInfo(array $taxes, $originalPrice)
    {
        throw new EkomApiException("deprecated, use applyTaxGroup instead");
    }


    /**
     * @return array of taxItem
     */
    public function getTaxesByTaxGroupName($taxGroupName)
    {

        return A::cache()->get("Module.Ekom.Api.Layer.TaxLayer.getTaxesByTaxGroupName.$taxGroupName", function () use ($taxGroupName) {

            return QuickPdo::fetchAll("
select 
t.label,
t.id as tax_id,
t.amount,
h.order,
h.mode,
g.label as group_label,
g.condition

from ek_tax t 
inner join ek_tax_group_has_tax h on h.tax_id=t.id
inner join ek_tax_group g on g.id=h.tax_group_id

where g.name=:name
        
order by h.order asc
        
        ", [
                'name' => $taxGroupName,
            ]);
        });
    }


    /**
     * @param array $taxes , a taxesArray, as defined in the comments of the
     * TaxLayer.getTaxesByCardId method.
     *
     * @param $details , an array showing which tax were really applied (conditions might
     * have discarded some taxes)
     *      The details is an array of nodes.
     *      A node is an organizational unit of taxes, which is so that if you pass the price
     *      through the node chain, you obtain the desired price at the end of the chain.
     *
     *      Basically, taxes can be either merged or chained, and a node is either a group of merged taxes,
     *      or a tax of type chain.
     *
     *      Each node has the following structure:
     *          - amount: the percentage applied for that node (which in case of merged taxed is the sum of all taxes)
     *          - labels: an array of labels of taxes used for that node
     *          - ids: an array of ids of taxes used for that node
     *          - groupLabel: the label (bo label) of the tax group
     *          - priceBefore: the price before entering the node
     *          - priceAfter: the price after exiting the node
     *
     *
     * @return float: the price with taxes applied
     */
    public function applyTaxesToPrice(array $taxes, $price, array &$details = [])
    {
        $ret = E::trimPrice($price);


        // now apply the dumb tax logic
        // first find the nodes, a node is the compound sum of two or more taxes merged together.
        // For instance, the node for taxA=20% and taxB=10% would be node1=30% (20+10)
        // then apply the nodes sequentially (one after the other)
        $nodes = [];
        $c = 0;
        foreach ($taxes as $tax) {
            if (0 !== $c && 'merge' === $tax['mode']) {
                $n = count($nodes) - 1;
                $nodes[$n]["amount"] += E::trimPrice($tax['amount']);
                $nodes[$n]["labels"][] = $tax['label'];
                $nodes[$n]["ids"][] = $tax['tax_id'];
            } else {
                $nodes[] = [
                    "amount" => E::trimPrice($tax['amount']),
                    "labels" => [$tax['label']],
                    "ids" => [$tax['id']],
                ];
            }
            $c++;
        }

        foreach ($nodes as $info) {
            $priceBefore = E::trimPrice((float)$ret);
            $amount = $info["amount"];
            $ret += E::trimPrice($ret * $amount / 100);
            $priceAfter = $ret;
            $info['priceBefore'] = $priceBefore;
            $info['priceAfter'] = $priceAfter;
            $info["amount"] = (float)$info["amount"];
            $details[] = $info;
        }
        return $ret;
    }


    private static function getTaxGroupInfoByRows(array $rows)
    {
        $ret = false;
        if ($rows) {
            $ret = [];
            $groupSet = false;
            $taxes = [];
            foreach ($rows as $row) {
                if (false === $groupSet) {
                    $ret['id'] = $row['group_id'];
                    $ret['name'] = $row['group_name'];
                    $ret['label'] = $row['group_label'];
                    $groupSet = true;
                }
                $taxes[] = [
                    "id" => $row['tax_id'],
                    "label" => $row['label'],
                    "amount" => $row['amount'],
                    "order" => $row['order'],
                    "mode" => $row['mode'],
                ];
            }
            $ret["taxes"] = $taxes;
        }
        return $ret;
    }


    private static function decorateQueryWithRuleConditionByTaxContext(string &$q, array &$markers, array $taxContext)
    {
        /**
         * Assuming the "where 1" is already set...
         */
        if (array_key_exists("user_group_id", $taxContext)) {
            $q .= " and cond_user_group_id = " . (int)$taxContext['user_group_id'];
        } else {
            $q .= "and cond_user_group_id is null";
        }
        for ($i = 1; $i <= 4; $i++) {
            $name = "extra" . $i;
            if (array_key_exists($name, $taxContext)) {
                $marker = "taxmarker$i";
                $q .= " and cond_$name = :$marker";
                $markers[$marker] = $taxContext[$name];
            } else {
                $q .= "and cond_$name is null";
            }
        }
    }


}