<?php


namespace Module\Ekom\Utils;


use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Bat\UriTool;
use Core\Services\A;
use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Notifier\EkomNotifier;
use Module\Ekom\Session\EkomSession;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use Umail\UmailInterface;

class E
{

    private static $conf = null;

    /**
     * Return the userId, or a default value, or throws an exception.
     *
     * @param mixed $default ,
     *                  define the behaviour if the user is not connected:
     *                  - false: throws an exception
     *                  - any other value: the default value to return
     * @return bool|null
     */
    public static function getUserId($default = false)
    {
        return EkomApi::inst()->connexionLayer()->getUserId($default);
    }

    /**
     * Return true if it's b2b, false otherwise
     */
    public static function isB2b()
    {
        return EkomApi::inst()->userLayer()->hasGroup("b2b");
//        return ('b2b' === EkomApi::inst()->configLayer()->getBusinessType()) ? true : false; // old code
    }

    public static function getShopId()
    {
        EkomApi::inst()->initWebContext();
        return (int)ApplicationRegistry::get("ekom.shop_id");
    }

    /**
     * This should be the only method used to check whether or not the ekom customer
     * is connected or not.
     * (because the implementation could change in the future)
     * Todo: remove SessionUser::isConnected instances found in the ekom scope...
     */
    public static function userIsConnected()
    {
        return EkomApi::inst()->connexionLayer()->isConnected();
    }


    /**
     * pick up the referer from session once (and remove the corresponding session key)
     */
    public static function pickUpReferer()
    {
        $target = EkomSession::get("referer");


        if (null !== $target) {
            /**
             * The http referer might be different but useful things, like:
             * - the checkout page
             */
            EkomSession::remove('referer');

        } else {
            $target = UriTool::getWebsiteAbsoluteUrl();
        }
        return $target;
    }

    /**
     *
     *
     * http://www.thesempost.com/new-title-description-lengths-for-google-seo/
     *
     * - meta_title: 70 chars
     * - meta_description: 100 chars
     *
     */
    public static function seo($title = null, $description = null, $keywords = null)
    {
        if (null !== $title) {
            HtmlPageHelper::$title = $title;
        }
        if (null !== $description) {
            HtmlPageHelper::$description = $description;
        }

        if (is_array($keywords)) {
            HtmlPageHelper::addMeta([
                "name" => "keywords",
                "content" => implode(',', $keywords),
            ]);
        }
    }

    public static function notify($eventName)
    {
        /**
         * @var $notifier EkomNotifier
         */
        $notifier = X::get("Ekom_notifier");
        return call_user_func_array([$notifier, "notify"], func_get_args());
    }

    public static function subscribe($eventName, callable $handler)
    {
        /**
         * @var $notifier EkomNotifier
         */
        $notifier = X::get("Ekom_notifier");
        return $notifier->subscribe($eventName, $handler);
    }

    public static function loadEkomJsApi()
    {
        X::get("Ekom_jsApiLoader")->load();
    }

    public static function link($routeId, array $params = [], $absolute = false, $https = null)
    {
        return A::link($routeId, $params, $absolute, $https);
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
        if (array_key_exists('subject', $params)) {
            $subject = $params['subject'];
        } else {

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
        }


        if (array_key_exists('commonVars', $params)) {
            $commonVars = $params['commonVars'];
        } else {
            $commonVars = [];
        }


        /**
         * @var $mail UmailInterface
         */
        $mail = X::get("Core_umail");
        $tplName = "Ekom/front/$type";

        $params = array_replace([
            "to" => null,
            "subject" => $subject,
            "vars" => function ($email) {
                return [];
            },
            "commonVars" => $commonVars,
        ], $params);


        // providing siteName var for free
        $siteName = XConfig::get("Application.site.name");
        if (false === array_key_exists("siteName", $params['commonVars'])) {
            $params['commonVars']['siteName'] = $siteName;
        }

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
            $shopId = ApplicationRegistry::get("ekom.shop_id");

            // host contextual file
            $f = ApplicationParameters::get("app_dir") . "/config/modules/Ekom/shop/$shopId.conf.php";
            if (file_exists($f)) {
                $conf = [];
                include $f;
                self::$conf = $conf;

            } else {
                $shopId = ApplicationRegistry::get("ekom.shop_id");
                XLog::error("[Ekom module] - E: host contextual file not found: $f, for shop $shopId");
            }

            // host-currency contextual file
            $f = ApplicationParameters::get("app_dir") . "/config/modules/Ekom/shop/$shopId/currency/$ciso.conf.php";
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

        throw new \Exception("Key not found in conf: $key, (Temporary exception: todo remove)");
        return $default;
    }


    public static function getImgBaseUri()
    {
        /**
         * Just in case you wonder:
         * the default ekom's image directory for products, cards and categories
         * is here:
         *
         *         /www/modules/Ekom/img
         */
        return '/modules/Ekom/img';
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