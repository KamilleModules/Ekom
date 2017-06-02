<?php


namespace Module\Ekom\Utils;


use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Kamille\Utils\Routsy\LinkGenerator\ApplicationLinkGenerator;
use Module\Ekom\Api\EkomApi;
use Umail\Umail;
use Umail\UmailInterface;

class E
{

    private static $conf = null;

    public static function link($routeId, array $params = [])
    {
        return ApplicationLinkGenerator::getUri($routeId, $params);
    }

    public static function passEncrypt($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }


    /**
     * Sometimes, price get multiplied by a rate (for instance tax rate),
     * and all the sudden it starts having more than 2 decimals,
     * which makes it un-intuitive to work with.
     *
     * I like all prices to have 2 decimals max, no exception.
     *
     * This method helps you do that.
     * It rounds to the closest 2 decimals number,
     * so:
     * - 19.366 is rounded to 19.37
     * - 19.364 is rounded to 19.36
     * - 19.365 is rounded to 19.36
     *
     *
     */
    public static function trimPrice($price)
    {
        return (float)round($price, 2, \PHP_ROUND_HALF_DOWN);
    }

    public static function trimPercent($percent)
    {
        return (float)round($percent, 2, \PHP_ROUND_HALF_DOWN);
    }

    /**
     * Send a front office mail
     */
    public static function sendMail($type, array $params)
    {
        $subject = null;
        switch ($type) {
            case 'accountCreated':
                $subject = "{siteName}: Your account has been created";
                break;
            default:
                XLog::error("[Ekom module] - E::sendMail: Unknown mail type: $type");
                return false;
                break;
        }


        /**
         * @var $mail UmailInterface
         */
        $mail = X::get("Core_umail");
        $tplName = "Ekom/front/$type";
        $siteName = XConfig::get("Application.site.name");

        $params = array_replace([
            "to" => null,
            "subject" => $subject,
            "vars" => function ($email) {
                return [];
            },
            "commonVars" => [
                'siteName' => $siteName,
            ],
        ], $params);


//        $logoFile = __DIR__ . "/myshop-logo.jpg";
//        $commonVars = [
//            'shop_name' => 'my shop',
//            'shop_url' => 'http://my_shop.com',
//            'shop_logo' => $mail->embedFile($logoFile),
//        ];


        $expected = 1;
        if (is_array($params['to'])) {
            $expected = count($params['to']);
        }
        $res = $mail->to($params['to'])
            ->from(XConfig::get("Application.email.from"))
            ->subject($subject)
            ->setVars($params['commonVars'], $params['vars'])
            ->setTemplate($tplName)
            ->send();

        return ($res === $expected);


    }

    /**
     * Return a price formatted according to the shop settings.
     */
    public static function price($number)
    {
        $moneyFormatArgs = self::conf("moneyFormatArgs");
        return self::formatPrice($number, $moneyFormatArgs);
    }


    public static function conf($key, $default = null)
    {

        EkomApi::inst()->initWebContext();
        if (null === self::$conf) {
            self::$conf = [];

            $host = ApplicationRegistry::get('ekom.host');
            $ciso = strtolower(ApplicationRegistry::get('ekom.currency_iso'));


            // host contextual file
            $f = ApplicationParameters::get("app_dir") . "/config/modules/Ekom/shop/$host.conf.php";
            if (file_exists($f)) {
                $conf = [];
                include $f;
                self::$conf = $conf;

            } else {
                $shopId = ApplicationRegistry::get("ekom.shop_id");
                XLog::error("[Ekom module] - E: host contextual file not found: $f, for shop $shopId");
            }

            // host-currency contextual file
            $f = ApplicationParameters::get("app_dir") . "/config/modules/Ekom/shop/$host/currency/$ciso.conf.php";
            if (file_exists($f)) {
                $conf = [];
                include $f;
                self::$conf = array_merge(self::$conf, $conf);

            } else {
                $shopId = ApplicationRegistry::get("ekom.shop_id");
                XLog::error("[Ekom module] - E: host-currency contextual file not found: $f, for shop $shopId");
            }

            Hooks::call("Ekom_adaptContextualConfig", self::$conf);


        }


        if (array_key_exists($key, self::$conf)) {
            return self::$conf[$key];
        }
        return $default;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function formatPrice($number, array $options = [])
    {
        $options = array_replace([
            'alwaysShowDecimals' => true,
            'nbDecimals' => 2,
            'decPoint' => ".",
            'thousandSep' => "",
            'moneySymbol' => "€",
            'moneyFormat' => "vs", // v represents the value, s represents the money symbol
        ], $options);
        extract($options);

        $v = number_format($number, $nbDecimals, $decPoint, $thousandSep);
        if (false === $alwaysShowDecimals && $nbDecimals > 0) {
            $p = explode($decPoint, $v);
            $dec = array_pop($p);
            if (0 === (int)$dec) {
                $v = implode('', $p);
            }
        }
        $ret = str_replace([
            'v',
            's',
        ], [
            $v,
            $moneySymbol,
        ], $moneyFormat);
        return $ret;
    }
}