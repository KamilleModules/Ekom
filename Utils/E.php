<?php


namespace Module\Ekom\Utils;


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Bat\CaseTool;
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
use Module\Ekom\JsApiLoader\EkomJsApiLoader;
use Module\Ekom\Notifier\EkomNotifier;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\DataChange\EkomDataChangeDispatcher;
use Module\Ekom\Utils\EkomRootUser\EkomRootUser;
use Module\FishMailer\Util\FishMailerService\FishMailerService;
use Module\TeamMail\Api\Layer\TeamLayer;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use Umail\UmailInterface;

class E
{

    private static $conf = null;


    public static function uriSite()
    {
        return E::link("Ekom_home", [], true);
    }

    public static function cheaterConfig($type)
    {
        $ret = [];
//        return $ret;

        // dirty demo mode (no backoffice yet)
        $cheaterConfig = EkomSession::get("cheaterConfig", []);
        if (array_key_exists($type, $cheaterConfig)) {
            $ret = $cheaterConfig[$type];
        }
        return $ret;
    }

    public static function cacheMark($msg)
    {
        XLog::log($msg, "cache.log");
    }

    public static function dlog($msg)
    {
        XLog::log($msg, "debug.log");
    }

    /**
     * https://github.com/KamilleModules/Ekom/tree/master/doc/ekom-dispatcher/ekom-dispatcher.md
     */
    public static function dispatch($dataIdentifier)
    {
        /**
         * @var $dispatcher EkomDataChangeDispatcher
         */
        $dispatcher = X::get('Ekom_DataChangeDispatcher');
        $dispatcher->trigger("dataChange", $dataIdentifier);
    }

    public static function dataChange($dataIdentifier)
    {
        throw new \Exception("Use E::dispatch instead");
    }


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
        if (null !== ($userId = EkomRootUser::getCurrentUserId())) {
            return $userId;
        }
        return EkomApi::inst()->connexionLayer()->getUserId($default);
    }


    public static function getUserData($key, $default = false)
    {
        return EkomApi::inst()->connexionLayer()->getUserData($key, $default);
    }

    public static function isErroneousModel(array $m)
    {
        if (array_key_exists('errorCode', $m)) {
//            XLog::error("[Ekom module] E:isErroneousModel - a model error occurred: " . ArrayToStringTool::toPhpArray($m));
            return true;
        }
        return false;
    }

    /**
     * @return \Localys\LocalysInterface
     */
    public static function localys()
    {
        /**
         * I personally use this _l function that I find handy.
         * This should be the one place where you change the localys invocation
         * if you need it.
         * (i.e. all modules call E::localys, not _l)
         */
        return _l();
    }


    /**
     * Return true if it's b2b, false otherwise
     */
//    public static function isB2b()
//    {
//        return EkomApi::inst()->userLayer()->hasGroup("b2b");
//    }

    public static function slugify($word)
    {
        return CaseTool::toDog($word);
    }

    public static function getShopId($shopId = null)
    {
        if (null !== $shopId) {
            return (int)$shopId;
        }
        EkomApi::inst()->initWebContext();
        return (int)ApplicationRegistry::get("ekom.shop_id");
    }

    public static function getLangId($langId = null)
    {
        if (null !== $langId) {
            return (int)$langId;
        }
        EkomApi::inst()->initWebContext();
        return (int)ApplicationRegistry::get("ekom.lang_id");
    }

    public static function getCurrencyId($currencyId = null)
    {
        if (null !== $currencyId) {
            return (int)$currencyId;
        }
        EkomApi::inst()->initWebContext();
        return (int)ApplicationRegistry::get("ekom.currency_id");
    }

    public static function getCurrencyIso()
    {
        EkomApi::inst()->initWebContext();
        return ApplicationRegistry::get("ekom.currency_iso");
    }

    public static function getLangIso()
    {
        EkomApi::inst()->initWebContext();
        return ApplicationRegistry::get("ekom.lang_iso");
    }

    /**
     * This should be the only method used to check whether or not the ekom customer
     * is connected or not.
     * (because the implementation could change in the future)
     * Todo: remove SessionUser::isConnected instances found in the ekom scope...
     */
    public static function userIsConnected()
    {
        if (null !== ($userId = EkomRootUser::getCurrentUserId())) {
            return true;
        }
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
        /**
         * @var $loader EkomJsApiLoader
         */
        $loader = X::get("Ekom_jsApiLoader");
        $loader->load();
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
    public static function sendMail($mailType, $recipient, array $variables = [])
    {
        if (true === XConfig::get("Ekom.mailEnable")) {
            Hooks::call("Ekom_Mailer_decorateVariables", $variables, $mailType, $recipient);
            return FishMailerService::create()->sendMail($mailType, $recipient, $variables);
        }
        return true;
    }


    /**
     * Send a front office mail
     */
    public static function sendTeamMail($mailType, array $variables = [])
    {
        if (true === XConfig::get("Ekom.mailEnable")) {

            $recipient = "anonymous";
            Hooks::call("Ekom_Mailer_decorateVariables", $variables, $mailType, $recipient);

            $nbMailSent = 0;
            $contactList = TeamLayer::getContactItemsByMailType($mailType);
            foreach ($contactList as $info) {
                $recipient = $info['email'];
                $name = $info['name'];
                $variables['teammate_name'] = $name;
                $res = FishMailerService::create()->sendMail($mailType, $recipient, $variables);
                if (true === (bool)$res) {
                    $nbMailSent++;
                }
            }
            return $nbMailSent;
        }
        return 1;
    }

    /**
     * Return a price formatted according to the shop settings.
     */
    public static function price($number)
    {
        $moneyFormatArgs = self::conf("moneyFormatArgs");
        return self::formatPrice($number, $moneyFormatArgs);
    }


    /**
     * Don't confound lazyConfig and conf methods.
     *
     * - lazyConfig ask for modules to get the value corresponding to a given key via a hook
     * - conf looks in the static modules configuration to see if there is the value corresponding to the given key
     *
     * The main reason why lazyConfig exist is that I didn't want to put all my conf in static files,
     * because with the numbers of modules growing, the "service container" would be loaded with all
     * those config values upfront, and I thought it was unnecessary.
     *
     * Actually, the lazy config system is how I wanted the system to be in the first place,
     * but I just miss the implementation back then.
     * @todo-ling: lazyConfig should be the only config accessing system, remove E::conf
     *
     * Unless you need to access early config values (before the app is really booted, but the number of config
     * values will be reduced by a lot anyway, so do it)
     * Actually, I'm not even sure if modules are consulted before the app is booted...
     *
     *
     *
     */
    public static function lazyConfig($key, $default = null)
    {
        $defaultValue = 690879; // improbable default value
        $value = $defaultValue;
        Hooks::call("Ekom_lazyConfig_getValue", $value, $key);
        if ($defaultValue === $value) {
            $value = $default;
        }
        return $value;
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