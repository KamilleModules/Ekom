<?php


namespace Module\Ekom\Api;

use Module\Ekom\Api\Object\Address;
use Module\Ekom\Api\Object\BackofficeUser;
use Module\Ekom\Api\Object\Carrier;
use Module\Ekom\Api\Object\CartDiscount;
use Module\Ekom\Api\Object\CartDiscountLang;
use Module\Ekom\Api\Object\Category;
use Module\Ekom\Api\Object\CategoryHasDiscount;
use Module\Ekom\Api\Object\CategoryHasProductCard;
use Module\Ekom\Api\Object\CategoryLang;
use Module\Ekom\Api\Object\Country;
use Module\Ekom\Api\Object\CountryLang;
use Module\Ekom\Api\Object\Coupon;
use Module\Ekom\Api\Object\CouponHasCartDiscount;
use Module\Ekom\Api\Object\CouponLang;
use Module\Ekom\Api\Object\Currency;
use Module\Ekom\Api\Object\Discount;
use Module\Ekom\Api\Object\DiscountLang;
use Module\Ekom\Api\Object\Lang;
use Module\Ekom\Api\Object\Order;
use Module\Ekom\Api\Object\OrderHasOrderStatus;
use Module\Ekom\Api\Object\OrderStatus;
use Module\Ekom\Api\Object\OrderStatusLang;
use Module\Ekom\Api\Object\Product;
use Module\Ekom\Api\Object\ProductAttribute;
use Module\Ekom\Api\Object\ProductAttributeLang;
use Module\Ekom\Api\Object\ProductAttributeValue;
use Module\Ekom\Api\Object\ProductAttributeValueLang;
use Module\Ekom\Api\Object\ProductCard;
use Module\Ekom\Api\Object\ProductCardHasDiscount;
use Module\Ekom\Api\Object\ProductCardHasTaxGroup;
use Module\Ekom\Api\Object\ProductCardLang;
use Module\Ekom\Api\Object\ProductHasDiscount;
use Module\Ekom\Api\Object\ProductHasProductAttribute;
use Module\Ekom\Api\Object\ProductLang;
use Module\Ekom\Api\Object\Shop;
use Module\Ekom\Api\Object\ShopConfiguration;
use Module\Ekom\Api\Object\ShopHasCarrier;
use Module\Ekom\Api\Object\ShopHasCurrency;
use Module\Ekom\Api\Object\ShopHasLang;
use Module\Ekom\Api\Object\ShopHasProduct;
use Module\Ekom\Api\Object\ShopHasProductCard;
use Module\Ekom\Api\Object\ShopHasProductCardLang;
use Module\Ekom\Api\Object\ShopHasProductLang;
use Module\Ekom\Api\Object\Tax;
use Module\Ekom\Api\Object\TaxGroup;
use Module\Ekom\Api\Object\TaxGroupHasTax;
use Module\Ekom\Api\Object\TaxLang;
use Module\Ekom\Api\Object\Timezone;
use Module\Ekom\Api\Object\User;
use Module\Ekom\Api\Object\UserGroup;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Api\Object\UserHasUserGroup;

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
     * @return CartDiscount
     */
    public function cartDiscount()
    {
        return $this->getObject('cartDiscount');
    }
    /**
     * @return CartDiscountLang
     */
    public function cartDiscountLang()
    {
        return $this->getObject('cartDiscountLang');
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
     * @return CategoryLang
     */
    public function categoryLang()
    {
        return $this->getObject('categoryLang');
    }
    /**
     * @return Country
     */
    public function country()
    {
        return $this->getObject('country');
    }
    /**
     * @return CountryLang
     */
    public function countryLang()
    {
        return $this->getObject('countryLang');
    }
    /**
     * @return Coupon
     */
    public function coupon()
    {
        return $this->getObject('coupon');
    }
    /**
     * @return CouponHasCartDiscount
     */
    public function couponHasCartDiscount()
    {
        return $this->getObject('couponHasCartDiscount');
    }
    /**
     * @return CouponLang
     */
    public function couponLang()
    {
        return $this->getObject('couponLang');
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
     * @return DiscountLang
     */
    public function discountLang()
    {
        return $this->getObject('discountLang');
    }
    /**
     * @return Lang
     */
    public function lang()
    {
        return $this->getObject('lang');
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
     * @return OrderStatusLang
     */
    public function orderStatusLang()
    {
        return $this->getObject('orderStatusLang');
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
     * @return ProductAttributeLang
     */
    public function productAttributeLang()
    {
        return $this->getObject('productAttributeLang');
    }
    /**
     * @return ProductAttributeValue
     */
    public function productAttributeValue()
    {
        return $this->getObject('productAttributeValue');
    }
    /**
     * @return ProductAttributeValueLang
     */
    public function productAttributeValueLang()
    {
        return $this->getObject('productAttributeValueLang');
    }
    /**
     * @return ProductCard
     */
    public function productCard()
    {
        return $this->getObject('productCard');
    }
    /**
     * @return ProductCardHasDiscount
     */
    public function productCardHasDiscount()
    {
        return $this->getObject('productCardHasDiscount');
    }
    /**
     * @return ProductCardHasTaxGroup
     */
    public function productCardHasTaxGroup()
    {
        return $this->getObject('productCardHasTaxGroup');
    }
    /**
     * @return ProductCardLang
     */
    public function productCardLang()
    {
        return $this->getObject('productCardLang');
    }
    /**
     * @return ProductHasDiscount
     */
    public function productHasDiscount()
    {
        return $this->getObject('productHasDiscount');
    }
    /**
     * @return ProductHasProductAttribute
     */
    public function productHasProductAttribute()
    {
        return $this->getObject('productHasProductAttribute');
    }
    /**
     * @return ProductLang
     */
    public function productLang()
    {
        return $this->getObject('productLang');
    }
    /**
     * @return Shop
     */
    public function shop()
    {
        return $this->getObject('shop');
    }
    /**
     * @return ShopConfiguration
     */
    public function shopConfiguration()
    {
        return $this->getObject('shopConfiguration');
    }
    /**
     * @return ShopHasCarrier
     */
    public function shopHasCarrier()
    {
        return $this->getObject('shopHasCarrier');
    }
    /**
     * @return ShopHasCurrency
     */
    public function shopHasCurrency()
    {
        return $this->getObject('shopHasCurrency');
    }
    /**
     * @return ShopHasLang
     */
    public function shopHasLang()
    {
        return $this->getObject('shopHasLang');
    }
    /**
     * @return ShopHasProduct
     */
    public function shopHasProduct()
    {
        return $this->getObject('shopHasProduct');
    }
    /**
     * @return ShopHasProductCard
     */
    public function shopHasProductCard()
    {
        return $this->getObject('shopHasProductCard');
    }
    /**
     * @return ShopHasProductCardLang
     */
    public function shopHasProductCardLang()
    {
        return $this->getObject('shopHasProductCardLang');
    }
    /**
     * @return ShopHasProductLang
     */
    public function shopHasProductLang()
    {
        return $this->getObject('shopHasProductLang');
    }
    /**
     * @return Tax
     */
    public function tax()
    {
        return $this->getObject('tax');
    }
    /**
     * @return TaxGroup
     */
    public function taxGroup()
    {
        return $this->getObject('taxGroup');
    }
    /**
     * @return TaxGroupHasTax
     */
    public function taxGroupHasTax()
    {
        return $this->getObject('taxGroupHasTax');
    }
    /**
     * @return TaxLang
     */
    public function taxLang()
    {
        return $this->getObject('taxLang');
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
     * @return UserHasUserGroup
     */
    public function userHasUserGroup()
    {
        return $this->getObject('userHasUserGroup');
    }
}