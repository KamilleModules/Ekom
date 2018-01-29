<?php


namespace Module\Ekom\Back\Helper\ApplicationSanityCheck;

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
 *
 * Tokens
 * ----------
 *
 * - a:
 *      - severity: critical
 *      - description:
 *              an entry has been created in the ek_country table, but there is no entry created in ek_country_lang yet.
 *              Fix: create the corresponding entry in ek_country_lang
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
class ApplicationSanityCheckHelper
{


    /**
     *
     * @return array, the errors detected
     *
     */
    public static function check()
    {
        $errors = [];

        //--------------------------------------------
        // a
        //--------------------------------------------
        /**
         * Check that all entries in ek_country have at least one lang bound to it (in ek_country_lang)
         */
        $rows = QuickPdo::fetchAll("
select 
c.id, 
c.iso_code,
l.lang_id

from ek_country c 
left join ek_country_lang l on l.country_id=c.id 
where l.lang_id is null

        
        ");
        if (count($rows) > 0) {



            self::addError($errors, "a", "
The following entries in ek_country todo....            
            ", $rows);
        }
        return $errors;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function addError(array &$errors, $token, $description, $params)
    {
        $errors[] = [
            'token' => $token,
            'description' => $description,
            'params' => $params,
        ];
    }
}