<?php


namespace Module\Ekom\Api;

use Bat\SessionTool;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Api\Layer\ShopLayer;


/**
 * The ekom api.
 *
 */
class EkomApi extends GeneratedEkomApi
{
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
     * - ekom.front.lang_id
     * - ekom.front.currency_id
     *
     * So that they become available in the process code.
     *
     * Further more, one more variable is added to the ApplicationRegistry:
     *
     * - ekom.front.shop_id
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
    public function initWebPage()
    {

        SessionTool::start();


        $host = Z::request()->host();
        if (false !== ($shopRow = $this->shopLayer()->getShopInfoByHost($host))) {
            $shopId = $shopRow['shop_id'];
            $langId = $shopRow['lang_id'];
            $currencyId = $shopRow['currency_id'];
            $timezone = $shopRow['timezone'];


            if (!array_key_exists("ekom.front", $_SESSION)) {
                $_SESSION['ekom.front'] = [
                    'lang_id' => $langId,
                    'currency_id' => $currencyId,
                ];
            } else {
                if (
                    array_key_exists('lang_id', $_SESSION['ekom.front']) &&
                    array_key_exists('currency_id', $_SESSION['ekom.front'])
                ) {
                    $langId = $_SESSION['ekom.front']['lang_id'];
                    $currencyId = $_SESSION['ekom.front']['currency_id'];
                } else {
                    XLog::error("[Ekom module] - EkomApi: lang_id or currency_id not found in \$_SESSION[ekom.front]");
                }
            }

            date_default_timezone_set($timezone);
            ApplicationRegistry::set("ekom.front.shop_id", $shopId);
            ApplicationRegistry::set("ekom.front.lang_id", $langId);
            ApplicationRegistry::set("ekom.front.currency_id", $currencyId);

        } else {
            XLog::error("[Ekom module] - EkomApi: No shop found with host $host");
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
}