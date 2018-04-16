<?php


namespace Module\Ekom\Helper;

use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Layer\TaxRuleConditionLayer;
use Module\Ekom\Api\Object\TaxRuleConditionHasTax;
use QuickPdo\QuickPdo;

class TaxHelper
{

    public static function getTaxGroupsByCartModel(array $model, $nestBySeller = true)
    {

        $ret = [];
        $items = $model['items'];
        foreach ($items as $item) {

            $taxGroupName = $item['taxGroupName'];
            $taxGroupLabel = $item['taxGroupLabel'];
            if (!array_key_exists($taxGroupName, $ret)) {
                $ret[$taxGroupName] = [
                    'label' => $taxGroupLabel,
                    'items' => [],
                ];
            }
            /**
             * Note: maybe we need less info than the whole item?
             * At least my need was just the seller, and the total
             */
            if (true === $nestBySeller) {
                $seller = $item['seller'];

                if (!array_key_exists($seller, $ret[$taxGroupName]['items'])) {
                    $ret[$taxGroupName]['items'][$seller] = 0;
                }
                $ret[$taxGroupName]['items'][$seller] += $item['taxAmount'];
            } else {
                /**
                 * @todo-ling: implement this case
                 */
                // not my use case
                throw new \Exception("not implemented yet");
            }
        }
        return $ret;
    }


    public static function recreateTaxRuleConditionHasTaxBindingsAndUpdateRatio(int $taxRuleConditionId, array $taxIds, array $options = [])
    {
        $mode = $options['mode'] ?? "";
        QuickPdo::delete("ek_tax_rule_condition_has_tax", [
            ["tax_rule_condition_id", "=", $taxRuleConditionId],
        ]);

        /**
         * We will also recompute the ratio
         */
        $fakeValue = 1;

        $order = 0;
        foreach ($taxIds as $taxId) {
            TaxRuleConditionHasTax::getInst()->create([
                "tax_rule_condition_id" => $taxRuleConditionId,
                "tax_id" => $taxId,
                "mode" => $mode,
                "order" => $order,
            ]);


            // note: normally it should depend on the mode...,
            // I'm just adding taxes one after the other for now
            $percent = TaxLayer::getTaxAmountById($taxId);
            $fakeValue += $fakeValue * $percent / 100;


            $order++;
        }

        $ratio = $fakeValue;
        TaxRuleConditionLayer::updateRatioById($taxRuleConditionId, $ratio);

    }

}