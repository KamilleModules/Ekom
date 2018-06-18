<?php


namespace Module\Ekom\Models;

use Controller\Ekom\Back\Catalog\CardController;
use Module\Ekom\Api\Layer\CartItemBoxLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\ConnexionLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\PaymentMethodHandler\PaymentMethodHandlerInterface;


/**
 * This class is not used by the code, except as a documentation reference.
 */
class EkomModels
{


    /**
     * addressModel
     * ==================
     * - address_id: string
     * - libelle
     * - phone
     * - address
     * - city
     * - postcode
     * - supplement
     * - country
     * - country_id
     * - country_iso_code
     * //
     * - is_default_shipping_address, bool
     * - is_default_billing_address, bool
     * - fName, string: a full name, which format depends on some locale parameters
     * - fAddress, string: a full address, which format depends on some locale parameters
     *
     *
     */
    private function addressModel()
    {
        return [];
    }


    /**
     * carrierOfferModel
     * ==================
     * 2 forms:
     *
     * form1 (regular)
     * ----------------
     * - name: string, the name of the carrier
     * - label: string, the label of the carrier
     * - selected: bool: whether this carrierOffer should be pre-selected (assuming the carrier offers are displayed
     *          on a page)
     *
     * - estimated_delivery_text: null|string
     * - estimated_delivery_date: null|datetime|[datetimeStart, datetimeEnd]
     * - shipping_cost: string, the formatted shipping cost without tax
     * - shipping_cost_raw: number, the unformatted shipping cost without tax
     * - shipping_cost_tax_applied: string, the formatted shipping cost with applicable tax(es) applied
     * - shipping_cost_tax_applied_raw: number, the unformatted shipping cost with applicable tax(es) applied
     *
     * Note: assuming both estimated delivery date and delivery text are provided, the template will decide which
     * one to display.
     *
     *
     * form2 (erroneous)
     * ----------------
     * - name: string, the name of the carrier
     * - label: string, the label of the carrier
     * - selected: bool: whether this carrierOffer should be pre-selected (assuming the carrier offers are displayed
     *          on a page)
     * - errorCode: string, an error code that prevents the checkout process to complete. It's application specific.
     *
     *
     */
    private function carrierOfferModel()
    {
        return [];
    }


    /**
     * cartItemBoxModel
     * =====================
     * @see CartItemBoxLayer
     *
     *
     * - ...all properties of miniBoxModel @see EkomModels::miniBoxModel(), plus the following:
     *
     * - wholesale_price
     * - weight
     * - selected_attributes_info: array of attributes selected by the user, each of which:
     *      - attribute_name
     *      - attribute_label
     *      - value_name
     *      - value_label
     *
     *
     * - selected_product_details: map of user selected product details
     * - selected_product_details_info: array product details selected by the user, each of which:
     *      - detail_name
     *      - detail_label
     *      - value_name
     *      - value_label
     *      - ...more as you need
     *
     * - tax_details: array of tax applied to this product, each item:
     *      - value: in percent
     *      - label
     *      - mode: not used today, but represents the tax combining mode (in case there are multiple taxes assigned to this product)
     *      - amount: the amount of tax collected for this item
     *
     * - tax_amount: the amount of tax applied to this item
     * - tax_amount_formatted:
     *
     * - discount_details: array of discount items (as of today, only one discount is applied max per product, but this could change...). Each item:
     *      - label
     *      - type: f|p
     *      - value
     *      - amount
     *
     *
     * - product_uri_with_details
     *
     */
    private function cartItemBoxModel()
    {
        return [];
    }


    /**
     * cartModel
     * =================
     * @see CartLayer::getCartModel()
     *
     *
     *
     * - items: array of:
     *      - ...all properties of CartItemBoxModel @see EkomModels::cartItemBoxModel()
     *      - cart_quantity
     *      - real_quantity: the current stock quantity for this reference.
     *                          Note that the miniBoxModel has a quantity which also represents the stock quantity for this reference.
     *                          However, since the cart is stored in session, this quantity is frozen in the cart.
     *                          And so if the real stock for this reference changes, those changes are not updated in the cart.
     *                          But now, with the real_quantity property, you've got an up-to-date/live value of the reference's stock quantity.
     *      - cart_token
     *      - line_real_price
     *      - line_real_price_formatted
     *      - line_base_price
     *      - line_base_price_formatted
     *      - line_sale_price
     *      - line_sale_price_formatted
     *      - line_tax_details: array of tax label => tax amount for this line (the tax amount for one unit x quantity)
     *      - line_tax_amount: the amount of tax for this line
     *      - line_discount_amount: the amount of discount for this line
     *
     *
     * - cart_total_weight: in kg
     * - cart_total_quantity
     * - cart_total_tax_excluded
     * - cart_total_tax_excluded_formatted
     * - cart_total_tax_included
     * - cart_total_tax_included_formatted
     * - cart_discount_amount:
     * - cart_discount_amount_formatted
     * - cart_discount_details: array of discount label => line discount amount .
     *                              Note that in current Ekom we only have one discount per product max (this might change).
     * - cart_tax_amount
     * - cart_tax_amount_formatted
     * - cart_tax_details: the tax of the items of this cart, grouped by tax.
     *      It's an array of tax_label => item, each item:
     *          - tax_value
     *          - amount: sum of tax amounts of the cart items bound to this tax
     *          - amount_formatted
     *
     *
     * - carrier_id: null|int
     * - carrier_label:
     * - carrier_estimated_delivery_date: text, indicating the estimated delivery date
     * - carrier_error_code: an error code issued by the carrier, indicating why it couldn't deliver the items
     *
     *
     * - shipping_status: int (0|1|2|3|4): the status of the shipping process.
     * @dev, @template-dev, You can use the fact that the status code is 4 to know whether or not the shipping process was successfully
     *      applied to this order.
     *
     *      - 0: the weight of the cart is 0, this is not deliverable (maybe the cart contains only downloadable products).
     *      - 1: no carrier was found. This is a configuration error and shouldn't occur, unless you have no carriers
     *                  defined/registered in your database
     *      - 2: a carrier was found, but for some reasons it couldn't deliver the current cart.
     *                  Read carrier_error_code to investigate about the failure.
     *      - 3: a carrier was found, but the shippingInfo it returned are erroneous.
     *                  This should never happen.
     *                  If however it happens to you, contact the carrier developer and tell him to repair her carrier module.
     *      - 4: a carrier was found and could deliver.
     *
     *
     * - shipping_cost_tax_excluded
     * - shipping_cost_tax_excluded_formatted
     * - shipping_cost_tax_included
     * - shipping_cost_tax_included_formatted
     * - shipping_cost_discount_amount
     * - shipping_cost_discount_amount_formatted
     * - shipping_cost_tax_amount
     * - shipping_cost_tax_amount_formatted
     * - shipping_cost_tax_label
     * - shipping_cost_tax_name
     *
     *
     *
     *
     * - has_coupons: bool
     * - coupons_total: sum of all coupons.saving_amount
     * - coupons_total_formatted
     * - coupons: array of coupon id.
     *          Note: coupons have to be recomputed on every page refresh,
     *          because they are applied depending on conditions which might change
     *          with any condition, such as the number of items in the cart, the datetime, etc...
     *
     *          So basically, the system with coupons is:
     *              - first the user tries to add a coupon,
     *              - if the coupon is accepted, then:
     *                  - it is added to the coupons bag
     *                  - it is also added in the coupons array (see below) and its effects
     *                          are immediate.
     *
     * - coupons_details: array, @see couponDetailsModel
     *
     *
     * - order_total: cart_total_tax_included + shipping_cost_tax_included
     * - order_total_formatted
     * - order_grand_total: order_total - coupons_total
     * - order_grand_total_formatted
     * - order_tax_amount: cart_tax_amount + shipping_cost_tax_amount
     * - order_tax_amount_formatted
     * - order_discount_amount: cart_discount_amount + shipping_cost_discount_amount
     * - order_discount_amount_formatted
     * - order_saving_total: order_discount_amount + coupons_total
     * - order_saving_total_formatted
     *
     */
    private function cartModel()
    {
        return [];
    }


    /**
     * categoryModel
     * =================
     * - id:
     * - name:
     * - label:
     * - slug:
     *
     *
     */
    private function categoryModel()
    {
        return [];
    }

    /**
     * checkoutData
     * -------------
     * - user_id
     * - ?carrier_id              (only if at least an item needs to be shipped)
     * - ?shipping_address_id     (only if at least an item needs to be shipped)
     * - ?store_address_id        (only if the shipping_address_id is defined)
     * - billing_address_id
     * - payment_method_id
     * - ...your own
     *
     */
    private function checkoutData()
    {
        return [];
    }


    /**
     * connexionData
     * ===============
     *
     * @see ConnexionLayer::getConnexionDataByUserId()
     *
     *
     * (the base connexion data)
     * - id
     * - email
     * - user_group_id
     * - user_group_name
     * - user_group_label
     * - gender_id
     * - gender_name
     * - gender_label
     * - gender_long_label
     * - default_shipping_address_id: string|null
     * - default_shipping_country_id: string|null
     * - default_shipping_country: string|null
     * - default_billing_address_id: string|null
     * - default_billing_country_id: string|null
     * - default_billing_country: string|null
     *
     * (the extension of the connexion data)
     * - ...other properties set by modules,
     *              Ekom recommends that those are predictable for a given base connexionData array,
     *              unless your modules handle the connexionData update (@see ConnexionLayer::getConnexionDataByUserId())
     *
     *
     */
    private function connexionData()
    {

    }


    /**
     * couponDetailsModel
     * ===============
     *
     * array of couponDetailItem, each of which:
     *      - code: coupon code
     *      - seller_id: int|null, the seller id
     *      - seller_name: string|null, the seller name creating the coupon, or null if the coupon does not
     *              apply to a seller in particular.
     *      - label: coupon label
     *      - amount: the amount in euros
     *      - amount_formatted
     *      - target: string, 256 chars.
     *                   Can be used in any way you want.
     *                   However, ekom by default uses the following heuristics:
     *
     *                   - shipping_cost: the coupon applies to the shipping cost
     *                   - order: the coupon applies to the order total
     *
     *
     * @this-part below is deprecated
     *                   - <emptyString>
     *                           distribute the coupon amount equally amongst all sellers.
     *                           Note: we could also use a ratio proportional to the
     *                           amount of the order handled by a seller,
     *                           but as for now, this technique is not implemented.
     *
     *                   - seller:$sellerName
     *                           apply the coupon amount only to the $sellerName seller
     *      - details: array used internally be ekom
     *
     *
     *
     * The target can be one of:
     * - shipping_cost
     * - order
     *
     * @related: config/morphic/Ekom/back/discounts/coupon.form.conf.php
     *
     *
     *
     */
    private function couponDetailsModel()
    {

    }


    /**
     * couponInfoModel
     * ==================
     *
     * - ek_coupon.*
     * - seller_name
     * - seller_label
     *
     */
    private function couponInfoModel()
    {

    }

    /**
     * creditCartItem
     * ====================
     * - id
     * - user_id
     * - type  (VISA, ...given by ingenico Card_Brand)
     * - last_four_digits
     * - owner
     * - expiration_date
     * - alias
     * - active
     * - is_default: bool
     * - fExpirationDate: string
     * - expired: bool
     * - img: string, uri to a card icon
     * - label: string, enhanced version of the card type
     *
     *
     */
    private function creditCardItem()
    {
        return [];
    }


    /**
     * - id: int, the discount id
     * - label: string, the discount label
     * - code: string, the discount code (symbolic name)
     * - type: the discount type, can be one of:
     *      - p: percentage
     *      - f: fixed discount
     * - value: numeric, the discount amount (works along with the type)
     *
     */
    private function discountItem()
    {

    }

    /**
     * extendedCartModel
     * ====================
     *
     * - cart: @see EkomModels::cartModel()
     * - itemsGroupedBySeller: @see EkomModels::itemsGroupedBySeller()
     *
     */
    private function extendedCartModel()
    {

    }

    /**
     * invoiceModel
     * ====================
     *
     *
     * - shop_id: int
     * - user_id: int
     * - order_id: int
     * - seller_id: int
     * - label: string
     * - invoice_number: string
     * - invoice_number_alt: null|string
     * - invoice_date: datetime
     * - pay_identifier: string
     * - currency_iso_code:
     * - lang_iso_code:
     * - shop_host:
     * - amount:
     * - seller:
     *
     * - user_info:
     * ----- groups: comma separated group names
     * ----- id
     * ----- shop_id
     * ----- email
     * ----- pass
     * ----- pseudo
     * ----- first_name
     * ----- last_name
     * ----- date_creation
     * ----- mobile
     * ----- phone
     * ----- phone_prefix
     * ----- newsletter
     * ----- gender
     * ----- birthday
     * ----- active
     *
     * - seller_address: <shopPhysicalAddress> -- @see EkomModels::shopPhysicalAddress
     * - shipping_address: same as <orderModel.shipping_address> -- @see EkomModels::orderModel()
     * - billing_address: same as <orderModel.billing_address> -- @see EkomModels::orderModel()
     *
     * - invoice_details:
     * ----- cartModel scoped by seller -- @see EkomModels::cartModel()
     * ----- payment_method_id: the payment method id
     * ----- payment_method_name: the payment method name
     * ----- payment_method_details: array, depends on the chosen payment method handler
     * (only if carrier was involved)
     * ----- ?carrier_id:
     * ----- ?carrier_name:
     * ----- ?carrier_details: array
     *
     *
     */
    private function invoiceModel()
    {
        return [];
    }


    /**
     * itemsGroupedBySeller
     * ====================
     * @see CartUtil::getItemsGroupedBySeller()
     *
     * - seller_name
     *      - label: the seller label
     *      - has_tax: bool, Whether at least one item had the tax applied to it
     *      - total_weight: the total weight for this group (in kg)
     *      - total: the sum of the items sale_price
     *      - total_formatted
     *      - total_tax_amount: the total amount of tax collected for this seller
     *      - total_tax_amount_formatted
     *      - tax_details: an array of tax amounts grouped by tax for the current seller group.
     *                       It's an array of tax_label => tax_info, with tax_info:
     *                           - tax_amount: the sum of the individual tax amounts for each item of that seller group.
     *                           - tax_amount_formatted
     *
     *      - items: the items for the current seller
     */
    private function itemsGroupedBySeller()
    {
        return [];
    }


    /**
     *
     * miniBoxModel
     * -------------
     * @see MiniProductBoxLayer
     *
     *
     *      - ... all properties from the base query @see ProductQueryBuilderUtil::getBaseQuery()
     *
     *
     *      - has_tax: bool
     *      - is_novelty: bool, whether or not the product card has been marked as novelty
     *      - product_uri: link to the product page
     *      - image: uri of the image (size medium)
     *      - image_alt: alt attribute of the image
     *      - image_title: title attribute of the image (like legend, but defaults to the label if empty)
     *      - has_discount: bool
     *
     *      - original_price_formatted
     *      - real_price_formatted
     *      - base_price_formatted
     *      - sale_price_formatted
     *      - discount_value_formatted: which can be either in currency or percent
     *      - product_details: array of detailName => valueName
     *
     *
     */
    private function miniBoxModel()
    {

    }


    /**
     * orderModel
     * ====================
     *
     *
     * - reference: string
     * - user_id: int
     * - date: datetime
     * - amount: number, total amount of the order
     *
     *              cart_total_tax_included
     *              + shipping_cost_tax_included
     *              - order_saving_total (amount of coupons)
     *
     *
     * - (Note:)
     *          amount_without_tax = cart_total_tax_excluded + shipping_cost_tax_excluded
     *
     *          Note: the amount_without_tax doesn't take into account the coupons:
     *          I talked to our accounting guy, and basically: we don't care much about coupons most of the time,
     *          as far as displaying graphs in the backoffice is what we want to do.
     *          We keep the coupons amount (of course), but we can exclude it from the total amount.
     *          He even said that the shipping_cost also could be excluded from the amount total.
     *          Therefore, in order to ease the statistic data, we will have all those prices as part of the
     *          order table: cart_total_tax_excluded, shipping_cost_tax_excluded,
     *          and their "with tax included" version.
     *
     *          Also, by the way, since I provide cart_total_tax_excluded and shipping_cost_tax_excluded,
     *          we don't need the amount_without_tax anymore (just do the addition when you need it).
     *
     *
     * - cart_total_tax_excluded
     * - shipping_cost_tax_excluded
     *
     *
     * - coupon_saving: number, total amount of coupon for this order (it's a positive number, or zero)
     * - cart_quantity:
     *
     * (those are stats sugar, they are redundant with data found in order_details)
     * - shipping_country_iso_code: string, empty if shipping doesn't apply
     * - payment_method: string
     * - payment_method_extra: string
     *
     * - user_info: @see EkomModels::userInfoModel()
     * - store_info: @see EkomModels::storeInfoModel()
     * - shipping_address: array|false (false if shipping address doesn't apply) -- @see EkomModels::addressModel()
     * - billing_address: array -- @see EkomModels::addressModel()
     *
     *
     * - order_details:
     * ----- cartModel -- @see EkomModels::extendedCartModel()
     * ----- payment_method_id: the payment method id
     * ----- payment_method_name: the payment method name
     * ----- payment_method_label: the payment method label
     * ----- payment_method_details: array, depends on the chosen payment method handler @see PaymentMethodHandlerInterface
     *
     * (only if a carrier was used)
     * ----- ?carrier_id:
     * ----- ?carrier_name:
     * ----- ?carrier_tracking_number: string (can be empty)
     * ----- ?shipping_comment: string
     *
     * ----- ...your own, @see Hooks::Ekom_CheckoutOrderUtil_decorateOrderDetails()
     * - order_origin: the source of the order. This might be useful if your orders come from Marketplaces for instance.
     *                      Empty value means ekom (or you can specify ekom if you want)
     * - currency_iso_code: iso code 4217 of the currency for this order (ex: EUR)
     */
    private function orderModel()
    {
        return [];
    }


    /**
     *
     * productBoxModel (work in progress)
     * ====================
     *
     *
     * - product_id
     * - product_card_id
     * - product_reference_id
     * - product_card_type_name
     * - product_card_type_label
     * - seller_id
     * - seller_name
     * - seller_label
     * - reference
     * - internal_reference
     * - quantity
     * - out_of_stock_text
     * - label
     * - product_slug
     * - product_card_slug
     * - image_id
     * - image_legend
     * - tax_ratio
     * - codes
     * - popularity
     * - discount_id
     * - discount_label
     * - discount_type
     * - discount_value
     * - original_price
     * - real_price
     * - base_price
     * - sale_price
     * - manufacturer_id
     * - manufacturer_name
     * - description
     * - long_description
     * - meta_title
     * - meta_description
     * - meta_keywords
     * - wholesale_price
     * - weight
     * - active
     * - has_tax
     * - is_novelty
     * - product_uri
     * - image
     * - image_title
     * - image_alt
     * - has_discount
     * - original_price_formatted
     * - real_price_formatted
     * - base_price_formatted
     * - sale_price_formatted
     * - discount_value_formatted
     * - selected_product_details
     * - product_details_list: @see EkomModels::productModifiersListModel()
     * - product_uri_with_details
     * - images
     * - rating_average
     * - rating_nbVotes
     * - attributes_list: @see EkomModels::productModifiersListModel()
     *
     *
     */
    private function productBoxModel()
    {

    }


    /**
     * productModifiersListModel
     * =======================
     *
     * array of modifierName => array:
     *      - label: label of the modifier
     *      - values:
     *          - value
     *          - value_label
     *          - selected: 0|1
     *          - page_uri:
     *          - ajax_page_uri:
     *
     *
     */
    private function productModifiersListModel()
    {
        return [];
    }


    /**
     * sellerAddressModel
     * =====================
     * - ek_address.*
     * - country_iso_code
     * - country: the country label
     *
     */
    private function sellerAddressModel()
    {

    }


    /**
     * shippingContextModel
     * =====================
     *
     * - cartItems: the cart items (cartModel.items)
     * - cartWeight: number
     * - shopAddress: null|array:shopPhysicalAddress
     * - shippingAddress: null|array:addressModel representing the user shipping address
     *
     *
     * @see EkomModels::cartModel()
     * @see CartLayer
     * @see EkomModels::shopPhysicalAddress()
     * @see EkomModels::addressModel()
     *
     *
     * Note: even if the user has no address, and/or the shop has no address, some carriers might use
     * a flat rate. That explains why those properties can be null.
     */
    private function shippingContextModel()
    {

    }

    /**
     * shippingInfoModel
     * =====================
     * @see CarrierInterface
     *
     *
     * - ?estimated_delivery_text:
     * - ?estimated_delivery_date: null|datetime|[datetimeStart, datetimeEnd]
     * - shipping_cost: number, the cost of the shipping of the accepted products (without tax applied)
     *
     *
     * Note: you should provide at least either the delivery text or the delivery date.
     *
     *
     */
    private function shippingInfoModel()
    {

    }

    /**
     * shopPhysicalAddress
     * =====================
     *
     * @see ShopLayer
     *
     * - ek_address.*
     * - country_iso_code: the country iso code
     * - country: the country label
     *
     *
     */
    private function shopPhysicalAddress()
    {

    }


    /**
     * storeInfoModel
     * =================
     * - label
     * - id
     * - libelle
     * - phone
     * - address
     * - city
     * - postcode
     * - supplement
     * - active
     * - country_id
     * - country
     */
    private function storeInfoModel()
    {

    }


    /**
     * taxGroup
     * --------------
     * - rule_id
     * - rule_label
     * - ratio
     * - taxes:
     *      - 0:
     *          - id
     *          - label
     *          - amount
     *          - order
     *          - mode
     *      - ...
     */
    private function taxGroup()
    {

    }


    /**
     * userInfoModel
     * --------------
     *
     * Either:
     * - ek_user.*
     * - group_name
     * - group_label
     * - gender_name
     * - gender_label
     * - gender_long_label
     *
     * Or:
     *
     * - foreignOrigin: (the market place the user was imported from)
     * - email:
     *
     *
     */
    private function userInfoModel()
    {

    }


    /**
     * verticalMenuItem
     * --------------
     * @see CardController::getVerticalMenuItem()
     *
     *
     * - 0: string label
     * - 1: string uri
     * - 2: bool isSelected
     * - 2: bool isDisabled
     */
    private function verticalMenuItem()
    {

    }
}