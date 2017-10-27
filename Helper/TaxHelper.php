<?php


namespace Module\Ekom\Helper;

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

}