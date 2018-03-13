<?php


namespace Module\Ekom\Helper;


/**
 * The problem:
 *
 * when you insert data from a form to a DECIMAL field in the database,
 * the database uses the dot as decimal separator.
 *
 * As an human, I like to be able to use both the dot AND the comma.
 * Well, if I use the comma, this won't work as expected.
 * The class below provides some tools to implement the workaround for this problem.
 *
 *
 */
class DecimalValueHelper
{


    public static function decorate(array &$data, array $decimalFields)
    {
        foreach ($decimalFields as $field) {
            $data[$field] = str_replace(',', '.', $data[$field]);
        }
    }
}