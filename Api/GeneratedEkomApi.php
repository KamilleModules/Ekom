<?php


namespace Module\Ekom\Api;

use Module\Ekom\Api\Object\Address;
use Module\Ekom\Api\Object\BackofficeUser;
use Module\Ekom\Api\Object\Carrier;
use Module\Ekom\Api\Object\Cart;
use Module\Ekom\Api\Object\Category;
use Module\Ekom\Api\Object\CategoryHasDiscount;
use Module\Ekom\Api\Object\CategoryHasProductCard;
use Module\Ekom\Api\Object\Country;
use Module\Ekom\Api\Object\Coupon;
use Module\Ekom\Api\Object\Currency;
use Module\Ekom\Api\Object\Discount;
use Module\Ekom\Api\Object\Feature;
use Module\Ekom\Api\Object\FeatureValue;
use Module\Ekom\Api\Object\Gender;
use Module\Ekom\Api\Object\Invoice;
use Module\Ekom\Api\Object\Lang;
use Module\Ekom\Api\Object\Manufacturer;
use Module\Ekom\Api\Object\Newsletter;
use Module\Ekom\Api\Object\Order;
use Module\Ekom\Api\Object\OrderHasOrderStatus;
use Module\Ekom\Api\Object\OrderStatus;
use Module\Ekom\Api\Object\PasswordRecoveryRequest;
use Module\Ekom\Api\Object\PaymentMethod;
use Module\Ekom\Api\Object\Product;
use Module\Ekom\Api\Object\ProductAttribute;
use Module\Ekom\Api\Object\ProductAttributeValue;
use Module\Ekom\Api\Object\ProductBundle;
use Module\Ekom\Api\Object\ProductBundleHasProduct;
use Module\Ekom\Api\Object\ProductCard;
use Module\Ekom\Api\Object\ProductCardHasProductAttribute;
use Module\Ekom\Api\Object\ProductCardImage;
use Module\Ekom\Api\Object\ProductCardType;
use Module\Ekom\Api\Object\ProductComment;
use Module\Ekom\Api\Object\ProductGroup;
use Module\Ekom\Api\Object\ProductGroupHasProduct;
use Module\Ekom\Api\Object\ProductHasFeature;
use Module\Ekom\Api\Object\ProductHasProductAttribute;
use Module\Ekom\Api\Object\ProductHasProvider;
use Module\Ekom\Api\Object\ProductHasTag;
use Module\Ekom\Api\Object\ProductPurchaseStat;
use Module\Ekom\Api\Object\ProductPurchaseStatCategory;
use Module\Ekom\Api\Object\ProductReference;
use Module\Ekom\Api\Object\ProductReferenceHasDiscount;
use Module\Ekom\Api\Object\ProductVariation;
use Module\Ekom\Api\Object\Provider;
use Module\Ekom\Api\Object\Seller;
use Module\Ekom\Api\Object\SellerHasAddress;
use Module\Ekom\Api\Object\ShopConfiguration;
use Module\Ekom\Api\Object\Store;
use Module\Ekom\Api\Object\Tag;
use Module\Ekom\Api\Object\Tax;
use Module\Ekom\Api\Object\TaxRule;
use Module\Ekom\Api\Object\TaxRuleCondition;
use Module\Ekom\Api\Object\TaxRuleConditionHasTax;
use Module\Ekom\Api\Object\Timezone;
use Module\Ekom\Api\Object\User;
use Module\Ekom\Api\Object\UserGroup;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Api\Object\UserHasProduct;

use XiaoApi\Api\XiaoApi;


/**
 * IMPORTANT NOTE:
 * -------------------
 * This class is generated by a script, don't edit it manually, or you might loose
 * you changes on the next update.
 *
 *
 * The goal of this class is to generate explicit method names, so that you can benefit
 * your IDE's auto-completion features.
 */
class GeneratedEkomApi extends XiaoApi
{
    private static $inst;

    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }


    
    /**
     * @return Address
     */
    public function address()
    {
        return $this->getObject('address');
    }
    /**
     * @return BackofficeUser
     */
    public function backofficeUser()
    {
        return $this->getObject('backofficeUser');
    }
    /**
     * @return Carrier
     */
    public function carrier()
    {
        return $this->getObject('carrier');
    }
    /**
     * @return Cart
     */
    public function cart()
    {
        return $this->getObject('cart');
    }
    /**
     * @return Category
     */
    public function category()
    {
        return $this->getObject('category');
    }
    /**
     * @return CategoryHasDiscount
     */
    public function categoryHasDiscount()
    {
        return $this->getObject('categoryHasDiscount');
    }
    /**
     * @return CategoryHasProductCard
     */
    public function categoryHasProductCard()
    {
        return $this->getObject('categoryHasProductCard');
    }
    /**
     * @return Country
     */
    public function country()
    {
        return $this->getObject('country');
    }
    /**
     * @return Coupon
     */
    public function coupon()
    {
        return $this->getObject('coupon');
    }
    /**
     * @return Currency
     */
    public function currency()
    {
        return $this->getObject('currency');
    }
    /**
     * @return Discount
     */
    public function discount()
    {
        return $this->getObject('discount');
    }
    /**
     * @return Feature
     */
    public function feature()
    {
        return $this->getObject('feature');
    }
    /**
     * @return FeatureValue
     */
    public function featureValue()
    {
        return $this->getObject('featureValue');
    }
    /**
     * @return Gender
     */
    public function gender()
    {
        return $this->getObject('gender');
    }
    /**
     * @return Invoice
     */
    public function invoice()
    {
        return $this->getObject('invoice');
    }
    /**
     * @return Lang
     */
    public function lang()
    {
        return $this->getObject('lang');
    }
    /**
     * @return Manufacturer
     */
    public function manufacturer()
    {
        return $this->getObject('manufacturer');
    }
    /**
     * @return Newsletter
     */
    public function newsletter()
    {
        return $this->getObject('newsletter');
    }
    /**
     * @return Order
     */
    public function order()
    {
        return $this->getObject('order');
    }
    /**
     * @return OrderHasOrderStatus
     */
    public function orderHasOrderStatus()
    {
        return $this->getObject('orderHasOrderStatus');
    }
    /**
     * @return OrderStatus
     */
    public function orderStatus()
    {
        return $this->getObject('orderStatus');
    }
    /**
     * @return PasswordRecoveryRequest
     */
    public function passwordRecoveryRequest()
    {
        return $this->getObject('passwordRecoveryRequest');
    }
    /**
     * @return PaymentMethod
     */
    public function paymentMethod()
    {
        return $this->getObject('paymentMethod');
    }
    /**
     * @return Product
     */
    public function product()
    {
        return $this->getObject('product');
    }
    /**
     * @return ProductAttribute
     */
    public function productAttribute()
    {
        return $this->getObject('productAttribute');
    }
    /**
     * @return ProductAttributeValue
     */
    public function productAttributeValue()
    {
        return $this->getObject('productAttributeValue');
    }
    /**
     * @return ProductBundle
     */
    public function productBundle()
    {
        return $this->getObject('productBundle');
    }
    /**
     * @return ProductBundleHasProduct
     */
    public function productBundleHasProduct()
    {
        return $this->getObject('productBundleHasProduct');
    }
    /**
     * @return ProductCard
     */
    public function productCard()
    {
        return $this->getObject('productCard');
    }
    /**
     * @return ProductCardHasProductAttribute
     */
    public function productCardHasProductAttribute()
    {
        return $this->getObject('productCardHasProductAttribute');
    }
    /**
     * @return ProductCardImage
     */
    public function productCardImage()
    {
        return $this->getObject('productCardImage');
    }
    /**
     * @return ProductCardType
     */
    public function productCardType()
    {
        return $this->getObject('productCardType');
    }
    /**
     * @return ProductComment
     */
    public function productComment()
    {
        return $this->getObject('productComment');
    }
    /**
     * @return ProductGroup
     */
    public function productGroup()
    {
        return $this->getObject('productGroup');
    }
    /**
     * @return ProductGroupHasProduct
     */
    public function productGroupHasProduct()
    {
        return $this->getObject('productGroupHasProduct');
    }
    /**
     * @return ProductHasFeature
     */
    public function productHasFeature()
    {
        return $this->getObject('productHasFeature');
    }
    /**
     * @return ProductHasProductAttribute
     */
    public function productHasProductAttribute()
    {
        return $this->getObject('productHasProductAttribute');
    }
    /**
     * @return ProductHasProvider
     */
    public function productHasProvider()
    {
        return $this->getObject('productHasProvider');
    }
    /**
     * @return ProductHasTag
     */
    public function productHasTag()
    {
        return $this->getObject('productHasTag');
    }
    /**
     * @return ProductPurchaseStat
     */
    public function productPurchaseStat()
    {
        return $this->getObject('productPurchaseStat');
    }
    /**
     * @return ProductPurchaseStatCategory
     */
    public function productPurchaseStatCategory()
    {
        return $this->getObject('productPurchaseStatCategory');
    }
    /**
     * @return ProductReference
     */
    public function productReference()
    {
        return $this->getObject('productReference');
    }
    /**
     * @return ProductReferenceHasDiscount
     */
    public function productReferenceHasDiscount()
    {
        return $this->getObject('productReferenceHasDiscount');
    }
    /**
     * @return ProductVariation
     */
    public function productVariation()
    {
        return $this->getObject('productVariation');
    }
    /**
     * @return Provider
     */
    public function provider()
    {
        return $this->getObject('provider');
    }
    /**
     * @return Seller
     */
    public function seller()
    {
        return $this->getObject('seller');
    }
    /**
     * @return SellerHasAddress
     */
    public function sellerHasAddress()
    {
        return $this->getObject('sellerHasAddress');
    }
    /**
     * @return ShopConfiguration
     */
    public function shopConfiguration()
    {
        return $this->getObject('shopConfiguration');
    }
    /**
     * @return Store
     */
    public function store()
    {
        return $this->getObject('store');
    }
    /**
     * @return Tag
     */
    public function tag()
    {
        return $this->getObject('tag');
    }
    /**
     * @return Tax
     */
    public function tax()
    {
        return $this->getObject('tax');
    }
    /**
     * @return TaxRule
     */
    public function taxRule()
    {
        return $this->getObject('taxRule');
    }
    /**
     * @return TaxRuleCondition
     */
    public function taxRuleCondition()
    {
        return $this->getObject('taxRuleCondition');
    }
    /**
     * @return TaxRuleConditionHasTax
     */
    public function taxRuleConditionHasTax()
    {
        return $this->getObject('taxRuleConditionHasTax');
    }
    /**
     * @return Timezone
     */
    public function timezone()
    {
        return $this->getObject('timezone');
    }
    /**
     * @return User
     */
    public function user()
    {
        return $this->getObject('user');
    }
    /**
     * @return UserGroup
     */
    public function userGroup()
    {
        return $this->getObject('userGroup');
    }
    /**
     * @return UserHasAddress
     */
    public function userHasAddress()
    {
        return $this->getObject('userHasAddress');
    }
    /**
     * @return UserHasProduct
     */
    public function userHasProduct()
    {
        return $this->getObject('userHasProduct');
    }
}