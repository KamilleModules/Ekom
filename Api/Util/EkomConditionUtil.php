<?php


namespace Module\Ekom\Api\Util;


use BeeFramework\Notation\String\ShortCode\Tool\ShortCodeTool;

class EkomConditionUtil
{
    /**
     *
     * This is the EkomConditionsLanguage
     * ===========================
     *
     * By default, it matches, unless a condition fails.
     *
     * Conditions are expressed using the conditionString, which uses the
     * ShortCodeTool syntax.
     *
     * For instance:
     * dateMin=2018-03-20 00:00:00, dateMax=2019-02-01
     *
     * Available conditions are the following:
     *
     * - dateMin: date-ish
     * - dateMax: date-ish
     *
     *
     *
     *
     *
     *
     *
     *
     * @param $conditionString
     * @param array $context
     * @return bool=true
     */
    public static function match($conditionString, array $context = [])
    {


        $conditions = ShortCodeTool::parse($conditionString);
        a($conditions);
        if (array_key_exists("dateMin", $conditions)) {
            $dateMin = strtotime($conditions['dateMin']);
            if (time() < $dateMin) {
                return false;
            }
        }
        if (array_key_exists("dateMax", $conditions)) {
            $date = strtotime($conditions['dateMax']);
            if (time() > $date) {
                return false;
            }
        }
        if (array_key_exists("userGroup", $conditions)) {
            $group = strtotime($conditions['userGroup']);
            if (time() > $date) {
                return false;
            }
        }


        return true;
    }
}