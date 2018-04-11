<?php


namespace Module\Ekom\Helper;


use Core\Services\A;
use Module\Ekom\Utils\E;

class FrontTemplateInfoHelper
{

    /**
     *
     * About the info array:
     *
     * $info = [
     *      "carousels" => [
     *          "carousel-front" => "En bas de la page d'accueil",
     *      ],
     * ];
     *
     *
     *
     * Syntax:
     * ------------
     * - info:
     *      - carousels:
     *          - $groupName: string|array description, or array of localized descriptions.
     *                  If array, the keys of this array are lang iso code 3 letters (fra, eng, ...).
     *                  Description describes WHERE the carousel should be find (which page, and which
     *                  location if necessary)
     *
     *
     *
     *
     */
    public static function getTemplateInfo()
    {
        $ret = [];

        $frontTheme = E::getFrontTheme();
        $frontThemeFile = A::appDir() . "/theme/$frontTheme/ekom-template-info.php";
        if (file_exists($frontThemeFile)) {
            $info = [];
            include $frontThemeFile;
            return $info;
        }
        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param array $options :
     *          lang: the preferred lang
     * @return array
     */
    public static function getCarouselsLocations(array $options = [])
    {
        $ret = [];
        $templateInfo = self::getTemplateInfo();
        $carousels = $templateInfo['carousels'] ?? [];
        if ($carousels) {
            foreach ($carousels as $groupName => $carousel) {
                $ret[$groupName] = self::getLocalizedDescription($carousel, $options['lang'] ?? "eng");
            }
        }
        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getLocalizedDescription($thing, $preferredLang = null)
    {
        if (is_array($thing)) {
            if (array_key_exists($preferredLang, $thing)) {
                return $thing[$preferredLang];
            }
            return current($thing);
        }
        return $thing;
    }
}






