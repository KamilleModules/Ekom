<?php


namespace Module\Ekom\Back\Util\ApplicationSanityCheck;

use Module\Ekom\Exception\EkomException;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 *
 * The idea is to put in session a string containing 1-2 letter tokens.
 * Each token represents a potential problem that REQUIRES to be fixed by the admin user.
 * The correspondence between the tokens and the problem description is described below.
 *
 * The kind of problems spot by this diagnosis is for example:
 *
 * - an entry needs to be created in the menu ABC, level: critical.
 *
 * The level represents the severity of the problem.
 * It is encapsulated with the token name.
 *
 *
 *
 *
 * Severities
 * --------------
 * - critical: the application is broken until the problem is fixed.
 *              You should never put the app in prod before all critical problems are fixed.
 *
 *
 * Tags
 * -------
 * Tags are like variables for the translation string.
 * Tags are generated depending on the token.
 * They are indicated in the list below.
 *
 *
 *
 *
 * Tokens
 * ----------
 * Since some tokens have the same structure, I'll use functions.
 *
 * function missing( $tableName )
 *      - severity: critical
 *      - description:
 *              at least one entry is required in $tableName table
 *      - tags:
 *          - word: the word representing the object corresponding to the table
 *          - table: the name of the table (for instance ek_currency)
 *
 *
 *
 * function missingTranslation ( $table, $translationTable, $tipColumn )
 *      - severity: critical
 *      - description:
 *              an entry has been created in the $table table, but there is no entry created in $translationTable yet.
 *              Fix: create the corresponding entry in $translationTable
 *      - tags: codes (comma separated list of $tipColumn of missing countries)
 *
 *
 *
 *
 * - a: missingTranslation( ek_country, ek_country_lang, iso_codes )
 * - b: missing( ek_country )
 * - c: missing( ek_tax )
 * - d: missing( ek_tax_group )
 * - e: missing( ek_lang )
 * - f: missing( ek_seller )
 * - g: missing( ek_currency )
 * - h: missing( ek_shop )
 * - i: missing( ek_timezone )
 * - j: missing( ek_payment_method )
 * - k: missing( ek_category )
 * - l: missing( ek_product_type )
 * - m: missing( ek_product )
 * - n: missing( ek_product_card )
 * - o: missingTranslation( ek_category, ek_category_lang, name )
 *
 *
 *
 *
 *
 *
 *
 *
 * Translation
 * --------------
 * It is assumed that the description will be translated from the view.
 * However, we provide a default description in english.
 * The "params" parameter is used to build the default english description,
 * and passed as is so that the view can do the translation correctly.
 *
 *
 */
class ApplicationSanityCheckUtil
{


    private static $token2table = [
        "a" => "ek_country_lang",
        "b" => "ek_country",
        "c" => "ek_tax",
        "d" => "ek_tax_group",
        "e" => "ek_lang",
        "f" => "ek_seller",
        "g" => "ek_currency",
        "h" => "ek_shop",
        "i" => "ek_timezone",
        "j" => "ek_payment_method",
        "k" => "ek_category",
        "l" => "ek_product_type",
        "m" => "ek_product",
        "n" => "ek_product_card",
        "o" => "ek_category_lang",
    ];


    private static $removeWithInsert = [
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
    ];


    /**
     *
     * @param $lowestLevel : int=100
     *
     *          The minimum level of errors to display.
     *          Errors below this level won't be displayed.
     *
     *          - 100: critical
     *
     * @return array, the errors detected
     * @throws \Exception
     *
     */
    public static function check($lowestLevel = 100)
    {
        $errors = self::getSessionErrors();

        //--------------------------------------------
        // CRITICAL
        //--------------------------------------------
        if ($lowestLevel <= 100) {
            $criticalTokens = [
                'a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'm',
                'n',
                'o',
            ];
            foreach ($criticalTokens as $token) {
                $tags = [];
                if (false === self::executeTest($token, $tags)) {
                    $errors[$token] = $tags;
                } else {
                    unset($errors[$token]);
                }
            }
        }

        self::setSessionErrors($errors);
        return $errors;
    }


    /**
     * Use this method whenever a QuickPdo interaction is executed.
     * See Hook: Core_onQuickPdoInteractionAfter for more details,
     * or see QuickPdoInitializer for more details.
     *
     */
    public static function onQuickPdoDataAlterAfter($table, $activeMethod)
    {
        if (
            $activeMethod &&
            0 === strpos($table, 'ek_')
        ) {


            $errors = self::getSessionErrors();
            $nbMatch = 0;

            foreach ($errors as $token => $params) {
                $triggerTable = self::getTriggerTableByToken($token);
                if ($triggerTable === $table) {
                    $nbMatch++;


                    $tags = [];
                    if (false === self::executeTest($token, $tags)) {
                        $errors[$token] = $tags;
                    } else {
                        unset($errors[$token]);
                    }
                }
            }

            if ($nbMatch > 0) {
                EkomSession::set("sanity_check", $errors);
            }

            return $nbMatch;
        }

    }

    public static function getSessionErrors()
    {
        return EkomSession::get("sanity_check", []);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function setSessionErrors(array $errors)
    {
        EkomSession::set("sanity_check", $errors);
    }


    private static function removeError($token)
    {
        $errors = EkomSession::get("sanity_check", []);
        unset($errors[$token]);
        EkomSession::set("sanity_check", $errors);
    }


    private static function getTriggerTableByToken($token)
    {
        return self::$token2table[$token];
    }


    /**
     * @param $token
     * @param array $params : are fed only if the return of this method is false (the test failed)
     * @return bool
     * @throws EkomException
     */
    private static function executeTest($token, array &$tags = [])
    {
        $table = self::getTriggerTableByToken($token);
        switch ($token) {
            case "a":
                return self::executeMissingTranslationTest("ek_country", "ek_country_lang", "iso_code", "country_id", $tags);
                break;
            case "b":
            case "c":
            case "d":
            case "e":
            case "f":
            case "g":
            case "h":
            case "i":
            case "j":
            case "k":
            case "l":
            case "m":
            case "n":
                $count = (int)QuickPdo::fetch("select count(*) as count from $table", [], \PDO::FETCH_COLUMN);
                if ($count > 0) {
                    return true;
                }
                $tags["table"] = $table;
                $tags["word"] = self::getWordByTable($table);
                return false;
                break;
            case "o":
                return self::executeMissingTranslationTest("ek_category", "ek_category_lang", "name", "category_id", $tags);
                break;
            default:
                break;
        }
        throw new EkomException("Unknown token: $token");
    }


    private static function getWordByTable($table)
    {
        $p = explode("_", $table);
        array_shift($p);
        return implode(' ', $p);
    }


    private static function executeMissingTranslationTest($table, $translationTable, $column, $translationTableFk, array &$tags = [])
    {
        /**
         * Check that all entries in ek_country have at least one lang bound to it (in ek_country_lang)
         */
        $rows = QuickPdo::fetchAll("
select 
c.id, 
c.$column,
l.lang_id

from $table c 
left join $translationTable l on l.$translationTableFk=c.id 
where l.lang_id is null

        
        ");
        if (count($rows) > 0) {
            $codes = array_map(function ($v) use ($column) {
                return $v[$column];
            }, $rows);
            $sCodes = implode(', ', $codes);
            $tags = [
                'codes' => $sCodes,
            ];
            return false;
        }
        return true;
    }
}
