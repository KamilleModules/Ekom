<?php


namespace Module\Ekom\Api\Util;


class ConditionResolverUtil
{


    /**
     * @param $conditionString , the
     *      ekom discounts conditions language
     *      as described in database/discounts.md
     *
     * @param array $pool , an array of key => variable
     * @return bool, whether or not the condition string is successful
     */
    public static function evaluate($conditionString, array $pool)
    {
        a("evaluating conditionString: $conditionString");
        //--------------------------------------------
        // SIMPLIFY THE CONDITION STRING BY REMOVING PARENTHESIS
        //--------------------------------------------
        $c = 1;
        $pattern = '!\(\((.*?)\)\)!';
        $parenthesisBlocks = [];
        $expression = preg_replace_callback($pattern, function ($v) use (&$c, &$parenthesisBlocks) {
            $parenthesisBlocks[$c] = $v[1];
            return '$' . $c++;
        }, $conditionString);


        //--------------------------------------------
        // NOW RESOLVE EXPRESSION STRING
        //--------------------------------------------
        return self::evaluateExpression($expression, $pool, $parenthesisBlocks);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function evaluateExpression($conditionString, array $pool, $parenthesisBlocks)
    {
        a("evaluating expression: $conditionString");
        $andBlocks = preg_split('!(&&)!', $conditionString);
        foreach ($andBlocks as $andBlock) {
            if (false === self::evaluateAndBlock($andBlock, $pool, $parenthesisBlocks)) {
                return false;
            }
        }
        return true;
    }

    private static function evaluateAndBlock($andBlock, array $pool, $parenthesisBlocks)
    {
        a("evaluating AndBlock: $andBlock");
        $orBlocks = preg_split('!(\|\|)!', $andBlock);
        foreach ($orBlocks as $orBlock) {
            if (true === self::evaluateOrBlock($orBlock, $pool, $parenthesisBlocks)) {
                return true;
            }
        }
        return false;
    }

    private static function evaluateOrBlock($orBlock, array $pool, $parenthesisBlocks)
    {
        a("evaluating OrBlock: $orBlock");
        /**
         * Miss foreach
         */

        /**
         * First, search for parenthesisBlocks and evaluate them
         */
        $flattenComparisonBlock = preg_replace_callback('!\$([0-9]+)!', function ($v) use ($parenthesisBlocks, $pool) {
            $index = $v[1];
            if (array_key_exists($index, $parenthesisBlocks)) {
                return self::evaluateExpression($parenthesisBlocks[$index], $pool, $parenthesisBlocks);
            } else {
                return $v[0];
            }
        }, $orBlock);
        return self::evaluateComparisonBlock($flattenComparisonBlock, $pool);

    }

    private static function evaluateComparisonBlock($comparisonBlock, array $pool)
    {
        $random = rand(0, 1);
        /**
         * Note: the comparisonBlock could be just a string (for testing purposes),
         * like a, b, c, 1, 2, ...
         */
//        $comparisonBlock = trim($comparisonBlock);
        a("evaluating comparisonBlock: $comparisonBlock, random result=$random");
        return (bool)$random;
    }

}