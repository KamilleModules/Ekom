<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 * Models used by this class:
 *
 *
 * taxItem:
 *      - label: translated label of the tax
 *      - amount: in percent
 *      - order: order of this tax in the tax group
 *      - mode: how to combine this tax with the other taxes in the group
 *      - group_label: the bo label for the tax group owning this tax
 *      - group_name: the name for the tax group owning this tax
 */
class TaxLayer
{


    /**
     * @param array|false $taxGroup
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
            if (0.0 !== (float)$price) {
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
     * Return the taxGroup model for the given cardId, or false array if the card
     * is not bound to any tax group.
     *
     * The taxGroup model looks like this:
     *
     * - name
     * - label
     * - id
     * - taxes:
     *      - 0:
     *          - id
     *          - label
     *          - amount
     *          - order
     *          - mode
     *      - ...
     *
     *
     */
    public function getTaxGroupInfoByCardId($cardId, $shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        $cardId = (int)$cardId;

        return A::cache()->get("Module.Ekom.Api.Layer.TaxLayer.getTaxesByCardId-$shopId.$langId.$cardId", function () use ($shopId, $langId, $cardId) {

            $rows = QuickPdo::fetchAll("
select 
l.label,
t.id as tax_id,
t.amount,
h.order,
h.mode,
g.id as group_id,
g.name as group_name,
g.label as group_label

from 
ek_tax_lang l
inner join ek_tax t on t.id=l.tax_id
inner join ek_tax_group_has_tax h on h.tax_id=t.id
inner join ek_tax_group g on g.id=h.tax_group_id
inner join ek_shop_has_product_card hh on hh.shop_id=g.shop_id and hh.tax_group_id=g.id

where l.lang_id=$langId
and g.shop_id=$shopId
and hh.product_card_id=$cardId
        
order by h.order asc
        
        ");
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

        }, [
            "ek_tax",
            "ek_tax_group_has_tax",
            "ek_tax_group",
            "ek_shop_has_product_card",
        ]);
    }


    /**
     * @return array of taxItem
     */
    public function getTaxesByTaxGroupName($taxGroupName, $shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        return A::cache()->get("Module.Ekom.Api.Layer.TaxLayer.getTaxesByTaxGroupName.$shopId.$langId.$taxGroupName", function () use ($shopId, $langId, $taxGroupName) {

            return QuickPdo::fetchAll("
select 
l.label,
t.id as tax_id,
t.amount,
h.order,
h.mode,
g.label as group_label,
g.condition

from 
ek_tax_lang l
inner join ek_tax t on t.id=l.tax_id
inner join ek_tax_group_has_tax h on h.tax_id=t.id
inner join ek_tax_group g on g.id=h.tax_group_id

where l.lang_id=$langId
and g.shop_id=$shopId
and g.name=:name
        
order by h.order asc
        
        ", [
                'name' => $taxGroupName,
            ]);
        }, [
            "ek_tax",
            "ek_tax_group_has_tax",
            "ek_tax_group",
        ]);
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


}