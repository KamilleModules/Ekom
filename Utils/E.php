<?php


namespace Module\Ekom\Utils;


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Bat\CaseTool;
use Bat\HashTool;
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
use Module\Ekom\Api\Layer\ConnexionLayer;
use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\Api\Layer\ImageLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Helper\DateSegmentHelper;
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
    private static $userContext = null;


    /**
     *
     * @see ImageLayer::createImageCopy()
     * @return string
     */
    public static function getCardImgUriByImgId(int $imageId, $type = null)
    {
        /**
         * @todo-ling: create seo service...
         */
        if (null === $type) {
            $type = "medium";
        }
        $imageNumbers = str_split((string)$imageId);
        $hash = implode("/", $imageNumbers);
        return E::getImgBaseUri() . "/cp/$hash/$imageId-$type.jpg";
    }


    /**
     * In ekom, the monetary aspects of a product can change depending on the user.
     *
     * The monetary aspects includes:
     * - the price
     * - the discount
     * - the tax
     *
     * So for instance, the product A could cost 15$ for user A, but 12$ for user B because user B is registered
     * as a professional, whereas user A is just a regular visitor without privilege.
     * Another example, the tax 20% applies only for users which shipping address is in France.
     *
     *
     * The user context is represented by an array of variables.
     * The user context is implemented as a singleton: it is meant to be created once per process to ensure its consistency.
     *
     * However, at certain strategic key points, it can update.
     * For instance, when the user connects, or when the user changes her shipping address, ...
     * To propagate those user context changes, we use the refreshUserContext method of this class,
     * which has the effect to update the user context (and is the only method in ekom allowed to do so).
     *
     * This is the only time when the user context can be updated and deviate from its singleton nature.
     * Whenever this happens, it is recommended to consider refreshing the page if it helps ensuring the consistency
     * of the displayed products.
     *
     *
     * So, the user context encapsulates all possible user variables which influence the monetary aspect of products
     * such as the tax, the price, the discount applied to a product.
     *
     * From the user context derive 3 other contexts:
     *
     * - price context
     * - discount context
     * - tax context
     *
     * Those contexts depend on the user context such as a given user context always produces the
     * exact same derived contexts (price, discount and tax contexts).
     *
     * Therefore, the hashed version of the user context is a good candidate for a cache identifier for encapsulating
     * a product, and by extension a product list.
     *
     *
     *
     *
     *
     * @return array
     */
    public static function getUserContext()
    {
        if (null === self::$userContext) {
            self::buildUserContext();
        }
        return self::$userContext;
    }

    /**
     * This method is the only method allowed to change the user context in ekom.
     * Call this method whenever the user context changes:
     *
     * - the user disconnects from her account
     * - the user connects to her account
     * - the user is connected and changes some sensible data of her account
     *
     */
    public static function refreshUserContext()
    {
        if (E::userIsConnected()) {
            $userId = E::getUserId();

            E::dlog("calling E::refreshUserContext " . date("Y-m-d H:i:s"));
            // we need to update the connexionData, which is the source of the UserContext when the user is connected
            $connexionData = ConnexionLayer::buildConnexionDataByUserId($userId);
//            az(__FILE__, $connexionData, SessionUser::getAll());
            SessionUser::setValues($connexionData);
            CartUtil::getCart()->refreshCartItems();
        }
        self::buildUserContext();
    }

    private static function buildUserContext()
    {

        /**
         * Here we set the base user context,
         * and then pass it to modules in case they want to extend/override it.
         */
        $dateSegment = date('Y-m-d') . " 00:00:00"; // modules will decide, ekom provide one segment per day by default
        $userId = null;
        $connexionData = [];

        if (E::userIsConnected()) {
            $userId = E::getUserId();
            $connexionData = ConnexionLayer::getConnexionData();
            $userContext = [
                "time_segment" => $dateSegment,
                "user_group_id" => $connexionData['user_group_id'],
                "user_group_name" => $connexionData['user_group_name'],
                "shipping_country" => $connexionData['default_shipping_country'],
                "billing_country" => $connexionData['default_billing_country'],
            ];


        } else {
            $userContext = [
                "time_segment" => $dateSegment,
                "user_group_id" => null,
                "user_group_name" => null,
                "shipping_country" => null,
                "billing_country" => null,
            ];
        }

        /**
         * userId=null means the user is not connected and the connexionData is empty.
         * Note: it is recommended that you don't create new data with this hook,
         * but rather just use the data that you create in the
         * Ekom_Connexion_decorateUserConnexionData hook, which is called
         * only when the user connects or the data changes, whereas the hooks
         * below gets called on every page (i.e. you don't want to create an
         * unnecessary sql query on every request...).
         */
        Hooks::call("Ekom_UserContext_decorateUserContext", $userContext, $connexionData, $userId);
        self::$userContext = $userContext;
    }


//    public static function getProductBoxContext()
//    {
//        $pbc = ApplicationRegistry::get("Ekom.productBoxContext");
//        if (null === $pbc) {
//            $pbc = [];
//            Hooks::call("Ekom_collectProductBoxContext", $pbc);
//        }
//        return $pbc;
//    }

//    public static function getProductBoxContextHash()
//    {
//        HashTool::getHashByArray(self::getProductBoxContext());
//    }

    public static function getTaxContext()
    {
        $userContext = E::getUserContext();
        if (true === self::isBackOffice()) {
            return [
                "cond_user_group_id" => $userContext['user_group_id'],
                "cond_extra1" => null,
                "cond_extra2" => null,
                "cond_extra3" => null,
                "cond_extra4" => null,
            ];
        } else {


            $taxContext = [
                "cond_user_group_id" => $userContext['user_group_id'],
                "cond_extra1" => null,
                "cond_extra2" => null,
                "cond_extra3" => null,
                "cond_extra4" => null,
            ];
            Hooks::call("Ekom_decorateTaxContext", $taxContext, $userContext);
            return $taxContext;
        }
    }

    public static function getPriceContext(array $userContext = null)
    {
        if (true === self::isBackOffice()) {
            return [
                "cond_identifier" => null,
            ];
        } else {

            if (null === $userContext) {
                $userContext = E::getUserContext();
            }

            $priceContext = [
                "cond_identifier" => null,
            ];
            Hooks::call("Ekom_decoratePriceContext", $priceContext, $userContext);
            return $priceContext;
        }
    }

    public static function getDiscountContext(array $userContext = null)
    {
        if (true === self::isBackOffice()) {
            return [
                "datetime" => $userContext['time_segment'],
                "cond_user_group_id" => $userContext['user_group_id'],
                "cond_extra1" => null,
            ];
        } else {

            if (null === $userContext) {
                $userContext = E::getUserContext();
            }

            $priceContext = [
                /**
                 * Note for datetime,
                 * depending on your caching strategy you might want to create
                 * datetime segment (for instance two date segments per day).
                 *
                 * The bottom line is that you have to provide a datetime here anyway,
                 * so for instance if you implement a 2 date segments per day,
                 * you would return 2018-04-16 00:00:00 and 2018-04-16 12:00:00.
                 */
                "datetime" => $userContext['time_segment'],
                "cond_user_group_id" => $userContext['user_group_id'],
                "cond_extra1" => null,
            ];
            Hooks::call("Ekom_decoratePriceContext", $priceContext, $userContext);
            return $priceContext;
        }
    }

    public static function getFrontTheme()
    {
        return XConfig::get("Core.themeFront");
    }


    public static function isBackOffice()
    {

        return ApplicationRegistry::get("isBackoffice", false);
    }

    /**
     * Return the front office lang iso code (eng, fra, ...)
     */
    public static function lang()
    {
        return ApplicationRegistry::get("lang", E::conf("default_lang"));
    }

    /**
     * Return the front office currency iso code (EUR, USD, ...)
     */
    public static function currencyIsoCode()
    {
        return E::conf("currencyIsoCode");
    }

    /**
     * Return the front office currency symbol
     */
    public static function currencySymbol()
    {
        return E::conf("currencySymbol");
    }


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

    public static function getAllUserData()
    {
        return EkomApi::inst()->connexionLayer()->getConnexionData();
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
        return sprintf('%01.2f', (float)round($price, 2, \PHP_ROUND_HALF_DOWN));
    }

    public static function trimPercent($percent)
    {
        return (float)round($percent, 2, \PHP_ROUND_HALF_DOWN);
    }

    /**
     * Send a front office mail
     */
    public static function sendMail(string $template, string $recipient, array $variables = [])
    {
        if (true === XConfig::get("Ekom.mailEnable")) {
            Hooks::call("Ekom_Mailer_decorateVariables", $variables, $template, $recipient);
            return FishMailerService::create()->sendFishMailByTemplate($recipient, $template, $variables);
        }
        return true;
    }


    /**
     * Return a price formatted according to the shop settings.
     */
    public static function price($number, $convertPrice = true)
    {
        $moneyFormatArgs = self::conf("moneyFormatArgs");
        return self::formatPrice($number, $moneyFormatArgs);
    }


    /**
     * $currencyIsoCode: the currency iso code in which the price is defined
     */
    public static function priceFlat($number, $currencyIsoCode)
    {
        return number_format($number, 2, 2, " ") . " $currencyIsoCode";
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


        if (null === self::$conf) {
            self::$conf = [
                'default_lang' => XConfig::get("Ekom.default_lang"),
                'currencySymbol' => XConfig::get("Ekom.currencySymbol"),
                'currencyIsoCode' => XConfig::get("Ekom.currencyIsoCode"),
                'moneyFormatArgs' => XConfig::get("Ekom.moneyFormatArgs"),
                'acceptOutOfStockOrders' => XConfig::get("Ekom.acceptOutOfStockOrders"),
                'sessionTimeout' => XConfig::get("Ekom.sessionTimeout"),
            ];
//            Hooks::call("Ekom_adaptContextualConfig", self::$conf);
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


    public static function getEcpUri($action, array $extraParams = [])
    {

        $s = "/service/Ekom/ecp/api?action=" . $action;
        if ($extraParams) {
            $s .= '&' . http_build_query($extraParams);
        }
        return $s;
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