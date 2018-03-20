<?php


namespace Module\Ekom\Api;

use Bat\SessionTool;
use Core\Services\A;
use Http4All\Header\AcceptLanguageHelper;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\AjaxHandlerLayer;
use Module\Ekom\Api\Layer\AttributeLayer;
use Module\Ekom\Api\Layer\BreadcrumbsLayer;
use Module\Ekom\Api\Layer\BundleLayer;
use Module\Ekom\Api\Layer\CacheLayer;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\CategoryCoreLayer;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\CategoryNewLayer;
use Module\Ekom\Api\Layer\CheckoutLayer;
use Module\Ekom\Api\Layer\CommentLayer;
use Module\Ekom\Api\Layer\ConditionLayer;
use Module\Ekom\Api\Layer\ConnexionLayer;
use Module\Ekom\Api\Layer\CountryLayer;
use Module\Ekom\Api\Layer\CouponLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Layer\ImageLayer;
use Module\Ekom\Api\Layer\InvoicesLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ListBundleLayer;
use Module\Ekom\Api\Layer\OrderBuilderLayer;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\PasswordLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\ProductCardLangLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Layer\ProductCodeLayer;
use Module\Ekom\Api\Layer\ProductCommentLayer;
use Module\Ekom\Api\Layer\ProductGroupLayer;
use Module\Ekom\Api\Layer\ProductHelperLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Api\Layer\ProductSelectionLayer;
use Module\Ekom\Api\Layer\ProductTypeLayer;
use Module\Ekom\Api\Layer\RelatedProductLayer;
use Module\Ekom\Api\Layer\SearchResultsLayer;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\SpecialCategoryLayer;
use Module\Ekom\Api\Layer\StatusLayer;
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



    public function cleanInitCache()
    {
        SessionTool::destroyPartial("ekom");
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return AddressLayer
     */
    public function addressLayer()
    {
        return $this->getLayer('addressLayer');
    }

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
     * @return BreadcrumbsLayer
     */
    public function breadcrumbsLayer()
    {
        return $this->getLayer('breadcrumbsLayer');
    }

    /**
     * @return BundleLayer
     */
    public function bundleLayer()
    {
        return $this->getLayer('bundleLayer');
    }

    /**
     * @return CacheLayer
     */
    public function cacheLayer()
    {
        return $this->getLayer('cacheLayer');
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
     * @return CategoryCoreLayer
     */
    public function categoryCoreLayer()
    {
        return $this->getLayer('categoryCoreLayer');
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
     * @return CurrencyLayer
     */
    public function currencyLayer()
    {
        return $this->getLayer('currencyLayer');
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
     * @return LangLayer
     */
    public function langLayer()
    {
        return $this->getLayer('langLayer');
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
     * @return ProductBoxLayer
     */
    public function productBoxLayer()
    {
        return $this->getLayer('productBoxLayer');
    }


    /**
     * @return ProductCardLangLayer
     */
    public function productCardLangLayer()
    {
        return $this->getLayer('productCardLangLayer');
    }


    /**
     * @return ProductCardLayer
     */
    public function productCardLayer()
    {
        return $this->getLayer('productCardLayer');
    }

    /**
     * @return ProductCodeLayer
     */
    public function productCodeLayer()
    {
        return $this->getLayer('productCodeLayer');
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
     * @return RelatedProductLayer
     */
    public function relatedProductLayer()
    {
        return $this->getLayer('relatedProductLayer');
    }


    /**
     * @return SearchResultsLayer
     */
    public function searchResultsLayer()
    {
        return $this->getLayer('searchResultsLayer');
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
     * @return SpecialCategoryLayer
     */
    public function specialCategoryLayer()
    {
        return $this->getLayer('specialCategoryLayer');
    }

    /**
     * @return StatusLayer
     */
    public function statusLayer()
    {
        return $this->getLayer('statusLayer');
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