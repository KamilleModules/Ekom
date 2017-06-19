<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class TaxLayer
{

    /**
     * Return the array of possible taxes of the tax group to which the given cardId is bound.
     * Taxes are ordered by increasing order.
     *
     * Possible means some taxes might be discarded by the condition.
     *
     *
     *
     *
     * @param $cardId
     * @param null $shopId
     * @param null $langId
     * @return a taxesArray, containing taxArray items.
     *              Each taxArray contains the following entries:
     *              - label: translated label of the tax
     *              - amount: in percent
     *              - order: order of this tax in the tax group
     *              - mode: how to combine this tax with the other taxes in the group
     *              - group_label: the bo label for the tax group owning this tax
     *              - condition: whether or not this group applies (by default, it's empty and means yes)
     *
     */
    public function getTaxesByCardId($cardId, $shopId = null, $langId = null)
    {
        if (null === $shopId) {
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        if (null === $langId) {
            $langId = ApplicationRegistry::get("ekom.lang_id");
        }
        $langId = (int)$langId;
        $shopId = (int)$shopId;
        $cardId = (int)$cardId;

        return A::cache()->get("Module.Ekom.Api.Layer.TaxLayer.getTaxesByCardId-$shopId.$langId.$cardId", function () use ($shopId, $langId, $cardId) {

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
inner join ek_product_card_has_tax_group hh on hh.tax_group_id=g.id and g.shop_id=hh.shop_id

where l.lang_id=$langId
and g.shop_id=$shopId
and hh.product_card_id=$cardId
        
order by h.order asc
        
        ");
        }, [
            "ek_tax",
            "ek_tax_group_has_tax",
            "ek_tax_group",
            "ek_product_card_has_tax_group.create",
            "ek_product_card_has_tax_group.update.$shopId.$cardId",
            "ek_product_card_has_tax_group.delete.$shopId.$cardId",
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
        // filter by conditions
        $taxes = array_filter($taxes, function (array $tax) {
            return EkomApi::inst()->conditionLayer()->matchCondition($tax['condition'], "tax");
        });

        $ret = $price;
        // now apply the dumb tax logic
        // first find the nodes, a node is the compound sum of two or more taxes merged together.
        // For instance, the node for taxA=20% and taxB=10% would be node1=30% (20+10)
        // then apply the nodes sequentially (one after the other)
        $nodes = [];
        $c = 0;
        foreach ($taxes as $tax) {
            if (0 !== $c && 'merge' === $tax['mode']) {
                $n = count($nodes) - 1;
                $nodes[$n]["amount"] += $tax['amount'];
                $nodes[$n]["labels"][] = $tax['label'];
                $nodes[$n]["ids"][] = $tax['tax_id'];
            } else {
                $nodes[] = [
                    "amount" => $tax['amount'],
                    "labels" => [$tax['label']],
                    "ids" => [$tax['tax_id']],
                    "groupLabel" => $tax['group_label'],
                ];
            }
            $c++;
        }

        foreach ($nodes as $info) {
            $priceBefore = (float)$ret;
            $amount = $info["amount"];
            $ret += $ret * $amount / 100;
            $priceAfter = $ret;
            $info['priceBefore'] = $priceBefore;
            $info['priceAfter'] = $priceAfter;
            $info["amount"] = (float)$info["amount"];
            $details[] = $info;
        }
        return $ret;
    }
}