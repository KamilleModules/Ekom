<?php


namespace Module\Ekom\Api;

use Bat\SessionTool;
use Core\Services\A;
use Http4All\Header\AcceptLanguageHelper;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\ConditionLayer;
use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\Api\Layer\ImageLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Layer\UserLayer;
use QuickPdo\QuickPdo;


/**
 * The ekom api.
 *
 */
class EkomApi extends GeneratedEkomApi
{

    private $initialized;

    public function __construct()
    {
        parent::__construct();
        $this->initialized = false;
    }





    //--------------------------------------------
    //
    //--------------------------------------------
    protected function log($type, $message) // override me
    {
        XLog::log($type, $message);
    }


    /**
     * Should be called by every ekom web controller that displays a page.
     *
     * It creates the following variables in session if they don't already exist.
     *
     * - ekom.front
     * ----- lang_id
     * ----- currency_id
     *
     * Those variables are then passed to the ApplicationRegistry as:
     *
     * - ekom.lang_id
     * - ekom.currency_id
     *
     * So that they become available in the process code.
     *
     * Further more, one more variable is added to the ApplicationRegistry:
     *
     * - ekom.shop_id
     *
     * And finally the default timezone is set (sorry other modules).
     *
     *
     * The host is used as the trigger to retrieve those information (at least the first time)
     * from the database (or an equivalent cache).
     *
     * If the host cannot be recognized (for instance because there is no corresponding entry in the shop table),
     * then in the ApplicationRegistry, the variables won't exist, and the timezone won't be set.
     *
     * If that happens, an error message is also sent to the log.
     *
     *
     *
     */
    public function initWebContext()
    {
        if (false === $this->initialized) {
            $this->initialized = true;

            SessionTool::start();

            /**
             * Trying to get the shop id from the host
             */
            $host = Z::request()->host();
            $shopRow = A::cache()->get("Module.Ekom.Api.EkomApi.initWebContext.$host", function () use ($host) {
                return $this->shopLayer()->getShopInfoByHost($host);
            }, [
                "ek_shop.*",
                "ek_timezone.*",
            ]);


            if (false !== $shopRow) {


                /**
                 * We try to get the "host-shop_id-lang_id-currency_id",
                 * with which we can access the following:
                 * - currency_iso (will be used to grab the moneyFormatArgs)
                 * - exchange_rate: useful for displaying the prices
                 * - lang_iso: useful for translations
                 */
                $shopId = $shopRow['shop_id'];
                $langId = $shopRow['lang_id'];
                $currencyId = $shopRow['currency_id'];
                $timezone = $shopRow['timezone'];


                if (array_key_exists("ekom.front", $_SESSION)) {
                    $langId = $_SESSION['ekom.front']['lang_id'];
                    $currencyId = $_SESSION['ekom.front']['currency_id'];
                } else {
                    /**
                     * the user has no session yet, let's create one for him.
                     * Note that with this technique, if the user change its browser preferences
                     * after the first time it will not be taken into account (because we store
                     * her preferences in session), so she needs to delete here cookies to see
                     * changes applied (this is an edge case, you shouldn't worry too much about it).
                     */
                    if (null === $langId) {
                        // get the user browser's preferences
                        if (null !== ($lang = Z::request()->header("accept-language"))) {
                            $langIso = AcceptLanguageHelper::acceptLanguageToPreferredIso639_3($lang);
                            $langId = EkomApi::inst()->lang()->readColumn('id', [
                                ['iso_code', '=', $langIso],
                            ]);
                            if (false === $langId) {
                                XLog::error("[Ekom module] - EkomApi: cannot guess the lang from browser, with accepted-language=$lang");
                                $langId = 0;
                            }
                        }
                    }
                    $_SESSION['ekom.front'] = [
                        'lang_id' => $langId,
                        'currency_id' => $currencyId,
                    ];
                }


                /**
                 * Now let's grab our currency_iso, exchange_rate and lang_iso,
                 * those info don't depend on the user, but only on the shop configuration (the "host-lang_id-currency_id" triplet),
                 * so we can cache it :)
                 */
                $tripletRow = A::cache()->get("Module.Ekom.Api.EkomApi.initWebContext.quartet.$host-$shopId-$langId-$currencyId", function () use ($currencyId, $shopId, $langId) {

                    $currencyId = (int)$currencyId;
                    $currencyRow = QuickPdo::fetch("
select 
h.exchange_rate,
h.active,
c.iso_code
from ek_shop_has_currency h 
inner join ek_currency c on c.id=h.currency_id 
where 
h.shop_id=$shopId
and h.currency_id=$currencyId
                
                
                ");

                    $currencyIso = $currencyRow['iso_code'];
                    $currencyRate = $currencyRow['exchange_rate'];
                    $currencyActive = $currencyRow['active'];
                    if ("1" === $currencyActive) {


                        $langId = (int)$langId;
                        if (false !== ($langRow = QuickPdo::fetch("
select 
l.iso_code
from ek_lang l 
inner join ek_shop_has_lang h on h.lang_id=l.id
where h.shop_id=$shopId 
and h.lang_id=$langId
                    
                    "))
                        ) {

                            return [
                                "currency_iso" => $currencyIso,
                                "currency_rate" => $currencyRate,
                                "lang_iso" => $langRow['iso_code'],
                            ];
                        } else {
                            return "[Ekom module] - EkomApi: the lang $langId is not active for shop $shopId";
                        }


                    } else {
                        return "[Ekom module] - EkomApi: no active currency found for shop $shopId";
                    }
                }, [
                    "ek_shop_has_currency.*",
                    "ek_currency.*",
                    "ek_lang.*",
                    "ek_shop_has_lang.*",
                    "ek_shop.delete",
                    "ek_shop.update",
                ]);


                if (is_array($tripletRow)) {


                    date_default_timezone_set($timezone);
                    ApplicationRegistry::set("ekom.host", $host);
                    ApplicationRegistry::set("ekom.shop_id", $shopId);
                    ApplicationRegistry::set("ekom.lang_id", $langId);
                    ApplicationRegistry::set("ekom.lang_iso", $tripletRow['lang_iso']);
                    ApplicationRegistry::set("ekom.currency_id", $currencyId);
                    ApplicationRegistry::set("ekom.currency_iso", $tripletRow['currency_iso']);
                    ApplicationRegistry::set("ekom.currency_rate", $tripletRow['currency_rate']);


                } else {
                    XLog::error($tripletRow);
                }

            } else {
                XLog::error("[Ekom module] - EkomApi: No shop found with host $host");
            }

        }
    }

    public function cleanInitCache()
    {
        SessionTool::destroyPartial("ekom");
    }



    //--------------------------------------------
    //
    //--------------------------------------------

    /**
     * @return CategoryLayer
     */
    public function categoryLayer()
    {
        return $this->getLayer('categoryLayer');
    }

    /**
     * @return ProductLayer
     */
    public function productLayer()
    {
        return $this->getLayer('productLayer');
    }

    /**
     * @return ShopLayer
     */
    public function shopLayer()
    {
        return $this->getLayer('shopLayer');
    }

    /**
     * @return ImageLayer
     */
    public function imageLayer()
    {
        return $this->getLayer('imageLayer');
    }

    /**
     * @return TaxLayer
     */
    public function taxLayer()
    {
        return $this->getLayer('taxLayer');
    }

    /**
     * @return ConditionLayer
     */
    public function conditionLayer()
    {
        return $this->getLayer('conditionLayer');
    }

    /**
     * @return CartLayer
     */
    public function cartLayer()
    {
        return $this->getLayer('cartLayer');
    }

    /**
     * @return DiscountLayer
     */
    public function discountLayer()
    {
        return $this->getLayer('discountLayer');
    }

    /**
     * @return UserLayer
     */
    public function userLayer()
    {
        return $this->getLayer('userLayer');
    }


}