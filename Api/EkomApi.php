<?php


namespace Module\Ekom\Api;

use Bat\SessionTool;
use Core\Services\A;
use Http4All\Header\AcceptLanguageHelper;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\AjaxHandlerLayer;
use Module\Ekom\Api\Layer\AttributeLayer;
use Module\Ekom\Api\Layer\BundleLayer;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\CheckoutLayer;
use Module\Ekom\Api\Layer\CommentLayer;
use Module\Ekom\Api\Layer\ConditionLayer;
use Module\Ekom\Api\Layer\ConfigLayer;
use Module\Ekom\Api\Layer\ConnexionLayer;
use Module\Ekom\Api\Layer\CountryLayer;
use Module\Ekom\Api\Layer\CouponLayer;
use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Layer\ImageLayer;
use Module\Ekom\Api\Layer\InvoicesLayer;
use Module\Ekom\Api\Layer\ListBundleLayer;
use Module\Ekom\Api\Layer\OrderBuilderLayer;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\PasswordLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Layer\ProductCommentLayer;
use Module\Ekom\Api\Layer\ProductGroupLayer;
use Module\Ekom\Api\Layer\ProductHelperLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Api\Layer\ProductSelectionLayer;
use Module\Ekom\Api\Layer\ProductTypeLayer;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\UserGroupLayer;
use Module\Ekom\Api\Layer\UserHasGroupLayer;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Api\Layer\WishListLayer;
use Module\Ekom\Session\EkomSession;
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
     * Will set the time zone, and will put the following variables in the applicationRegistry:
     *
     * - ekom.host
     * - ekom.shop_id
     * - ekom.lang_id
     * - ekom.lang_iso
     * - ekom.currency_id
     * - ekom.currency_iso
     * - ekom.currency_rate
     *
     *
     *
     * Should be called by every ekom web controller that displays a page.
     *
     * It creates the following variables in session if they don't already exist.
     *
     * - ekom
     * ----- front
     * --------- lang_id
     * --------- currency_id
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
                "ek_shop",
                "ek_timezone",
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


                $frontVars = EkomSession::get("front", false);
                if (false !== $frontVars) {
                    $langId = $frontVars['lang_id'];
                    $currencyId = $frontVars['currency_id'];
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
                    EkomSession::set('front', [
                        'lang_id' => $langId,
                        'currency_id' => $currencyId,
                    ]);
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
                    "ek_shop_has_currency",
                    "ek_currency",
                    "ek_lang",
                    "ek_shop_has_lang",
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
     * @return AjaxHandlerLayer
     */
    public function ajaxHandlerLayer()
    {
        return $this->getLayer('ajaxHandlerLayer');
    }

    /**
     * @return AttributeLayer
     */
    public function attributeLayer()
    {
        return $this->getLayer('attributeLayer');
    }

    /**
     * @return BundleLayer
     */
    public function bundleLayer()
    {
        return $this->getLayer('bundleLayer');
    }

    /**
     * @return CartLayer
     */
    public function cartLayer()
    {
        return $this->getLayer('cartLayer');
    }


    /**
     * @return CarrierLayer
     */
    public function carrierLayer()
    {
        return $this->getLayer('carrierLayer');
    }

    /**
     * @return CategoryLayer
     */
    public function categoryLayer()
    {
        return $this->getLayer('categoryLayer');
    }


    /**
     * @return CheckoutLayer
     */
    public function checkoutLayer()
    {
        return $this->getLayer('checkoutLayer');
    }


    /**
     * @return CommentLayer
     */
    public function commentLayer()
    {
        return $this->getLayer('commentLayer');
    }

    /**
     * @return ConditionLayer
     */
    public function conditionLayer()
    {
        return $this->getLayer('conditionLayer');
    }

    /**
     * @return ConfigLayer
     */
    public function configLayer()
    {
        return $this->getLayer('configLayer');
    }

    /**
     * @return ConnexionLayer
     */
    public function connexionLayer()
    {
        return $this->getLayer('connexionLayer');
    }

    /**
     * @return CouponLayer
     */
    public function couponLayer()
    {
        return $this->getLayer('couponLayer');
    }

    /**
     * @return CountryLayer
     */
    public function countryLayer()
    {
        return $this->getLayer('countryLayer');
    }

    /**
     * @return DiscountLayer
     */
    public function discountLayer()
    {
        return $this->getLayer('discountLayer');
    }

    /**
     * @return FeatureLayer
     */
    public function featureLayer()
    {
        return $this->getLayer('featureLayer');
    }

    /**
     * @return ImageLayer
     */
    public function imageLayer()
    {
        return $this->getLayer('imageLayer');
    }


    /**
     * @return InvoicesLayer
     */
    public function invoicesLayer()
    {
        return $this->getLayer('invoicesLayer');
    }

    /**
     * @return ListBundleLayer
     */
    public function listBundleLayer()
    {
        return $this->getLayer('listBundleLayer');
    }

    /**
     * @return OrderBuilderLayer
     */
    public function orderBuilderLayer()
    {
        return $this->getLayer('orderBuilderLayer');
    }


    /**
     * @return OrderLayer
     */
    public function orderLayer()
    {
        return $this->getLayer('orderLayer');
    }


    /**
     * @return PasswordLayer
     */
    public function passwordLayer()
    {
        return $this->getLayer('passwordLayer');
    }


    /**
     * @return PaymentLayer
     */
    public function paymentLayer()
    {
        return $this->getLayer('paymentLayer');
    }


    /**
     * @return ProductCardLayer
     */
    public function productCardLayer()
    {
        return $this->getLayer('productCardLayer');
    }

    /**
     * @return ProductCommentLayer
     */
    public function productCommentLayer()
    {
        return $this->getLayer('productCommentLayer');
    }

    /**
     * @return ProductGroupLayer
     */
    public function productGroupLayer()
    {
        return $this->getLayer('productGroupLayer');
    }


    /**
     * @return ProductHelperLayer
     */
    public function productHelperLayer()
    {
        return $this->getLayer('productHelperLayer');
    }

    /**
     * @return ProductLayer
     */
    public function productLayer()
    {
        return $this->getLayer('productLayer');
    }


    /**
     * @return ProductSelectionLayer
     */
    public function productSelectionLayer()
    {
        return $this->getLayer('productSelectionLayer');
    }

    /**
     * @return ProductTypeLayer
     */
    public function productTypeLayer()
    {
        return $this->getLayer('productTypeLayer');
    }


    /**
     * @return SellerLayer
     */
    public function sellerLayer()
    {
        return $this->getLayer('sellerLayer');
    }


    /**
     * @return ShopLayer
     */
    public function shopLayer()
    {
        return $this->getLayer('shopLayer');
    }


    /**
     * @return TaxLayer
     */
    public function taxLayer()
    {
        return $this->getLayer('taxLayer');
    }

    /**
     * @return UserGroupLayer
     */
    public function userGroupLayer()
    {
        return $this->getLayer('userGroupLayer');
    }


    /**
     * @return UserHasGroupLayer
     */
    public function userHasGroupLayer()
    {
        return $this->getLayer('userHasGroupLayer');
    }

    /**
     * @return UserAddressLayer
     */
    public function userAddressLayer()
    {
        return $this->getLayer('userAddressLayer');
    }


    /**
     * @return UserLayer
     */
    public function userLayer()
    {
        return $this->getLayer('userLayer');
    }

    /**
     * @return WishListLayer
     */
    public function wishListLayer()
    {
        return $this->getLayer('wishListLayer');
    }


}