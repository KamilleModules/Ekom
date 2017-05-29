<?php


namespace Module\Ekom\Api\Layer;


class ConditionLayer
{

    /**
     * Evaluate the condition and returns whether or not it was successful.
     *
     *
     * @param $condition
     * @param null $type
     * @return bool
     */
    public function matchCondition($condition, $type = null)
    {
        if ('' === $condition) {
            return true;
        }
        switch ($type) {
            case 'tax':
                // todo: conditions for tax (TaxLayer.applyTaxesToPrice)
                break;
            default:
                break;
        }
        return false;
    }
}