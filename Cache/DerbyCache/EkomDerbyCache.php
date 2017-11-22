<?php


namespace Module\Ekom\Cache\DerbyCache;


use Chronos\Chronos;
use DerbyCache\WithRelatedFileSystemDerbyCache;
use Kamille\Services\XLog;


/**
 * Assuming knowledge of WithRelatedFileSystemDerbyCache.
 *
 *
 *
 * Ekom derby cache strategy
 * ====================================
 *
 * In ekom, every cache item starts with "Ekom".
 *
 * Cache items are organized in different areas.
 *
 * The goal is:
 *
 * - to have a flexible cache system allowing us to rebuild only cache areas that needs to be rebuilt,
 *          rather than having one atomic cache bloc that contains the whole application.
 *          That will be mostly useful for developing the application while benefiting cache.
 * - to create all caches before hand (at 04:00 am every day using a cron task while the site is in maintenance mode,
 *          so that after the maintenance, all caches are ready, and the app is very fast and reactive.
 *          Note: not all the caches, but the most important ones...
 *
 *
 * ### The currency was dropped
 * Note: not for the best reasont, but although ekom uses the shopId-langId-currencyId as a triplet
 * identifying the environment (or at least some part of it), as far as cache is concerned,
 * we've dropped the currency part for now (might come back maybe
 * later, or not) as we believe it's not necessary to have that much level of details, and we were short on time (!).
 * In other words, we will be able to delete cache items for a given shop and lang.
 * Now we don't care about currencies: they ALL will be deleted (included in the shopId-langId duet).
 * So, when you delete a shopId-langId, you also delete all the currencies within that context, that's just
 * how it works for now.
 *
 *
 *
 *
 * The various areas are following:
 *
 *
 * Shop
 * ------------
 *
 * The shop area contains the following caches.
 *
 * - Module.Ekom.Api.EkomApi.initWebContext.lee
 * - Module.Ekom.Api.EkomApi.initWebContext.quartet.1-1-1
 * - Ekom.CarrierLayer.getCarriers.1
 * - Ekom.ShopLayer.getPhysicalAddresses.1.1.
 *
 *
 * Groups
 * - Ekom.ProductGroupLayer.getProductIdsByGroup.1.homePage
 *
 *
 * Boxes
 * - Ekom/figure/productBox-1-1-18-18--0388d8c31abba75a2bc2b2f6d262033756339be2
 * - Ekom/figure/productBox-1-1-3776---166bddc7404eda6259ac28450084352ea284e8ac
 *
 *
 * BoxList
 * - Ekom.ProductBoxLayer.getProductBoxListByGroupName.1.1.homePage-b35b9931bb80fdbc00bf502b1645316c15e4fb08
 *
 *
 * Categories
 * - Ekom.CategoryLayer.getSubCategoriesByName.1.1.home.0.special
 * - Ekom.CategoryLayer.getSubCategoriesByName.1.1.equipement.1.
 * - Ekom/CategoryCoreLayer/getSelfAndChildren-1.1.formation.1--
 *
 *
 * (box-category interaction)
 * - Ekom.ProductCardLayer.getProductCardIdsByCategoryId.1.8
 * - Ekom/CategoryCoreLayer/getSelfAndParentsByCategoryId-1.1.8.-1--
 * - Ekom.AttributeLayer.getAvailableAttributeByCategoryId.1.1.8
 *
 *
 * Modules
 * - ...module dependent
 *
 *
 */
class EkomDerbyCache extends WithRelatedFileSystemDerbyCache
{
    private $debug;
    private $alwaysForceRegenerate;

    public function __construct()
    {
        parent::__construct();
        $this->debug = false;
        $this->alwaysForceRegenerate = false;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    public function setAlwaysForceRegenerate($alwaysForceRegenerate)
    {
        $this->alwaysForceRegenerate = $alwaysForceRegenerate;
        return $this;
    }


    public function get($cacheIdentifier, callable $cacheItemGenerator, $forceGenerate = false)
    {
        if (true === $this->alwaysForceRegenerate) {
            $forceGenerate = true;
        }
        return parent::get($cacheIdentifier, $cacheItemGenerator, $forceGenerate);
    }

    protected function hook($hookName, $argument) // override me
    {
        if (true === $this->debug) {
            $msg = "$hookName -- $argument";
            if ('onCacheStart' === $hookName) {
                Chronos::point($argument);
            }
            if ('onCacheEnd' === $hookName) {
                $time = number_format(Chronos::point($argument)[0], 5);
                $msg .= "--$time" . "s";
            }

            // filter too many lines in cache.log
            // assuming an end without create is a hit
            if ('onCacheEnd' === $hookName || 'onCacheCreate' === $hookName) {
                XLog::log($msg, "cache.log");
            }
        }
        parent::hook($hookName, $argument);
    }

    protected function isFigure($cacheIdentifier) // override me
    {
        return (0 === strpos($cacheIdentifier, "Ekom/figure/"));
    }
}