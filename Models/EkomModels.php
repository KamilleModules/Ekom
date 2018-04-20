<?php


namespace Module\Ekom\Models;

use Module\Ekom\Api\Layer\CartItemBoxLayer;


/**
 * This class is not used by the code, except as a documentation reference.
 */
class EkomModels
{


    /**
     * addressModel
     * ==================
     * - address_id: string
     * - first_name
     * - last_name
     * - phone
     * - phone_prefix
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
     *
     * - tax_details: array of tax applied to this product, each item:
     *      - value: in percent
     *      - label
     *      - mode: not used today, but represents the tax combining mode (in case there are multiple taxes assigned to this product)
     *      - amount: the amount of tax collected for this item
     *
     * - discount_details: array of discount items (as of today, only one discount is applied max per product, but this could change...). Each item:
     *      - label
     *      - type: f|p
     *      - value
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
     *
     *
     * - items: array of:
     *      - ...all properties of CartItemBoxModel @see EkomModels::cartItemBoxModel()
     *      - cart_quantity
     *      - cart_token
     *      - line_real_price
     *      - line_real_price_formatted
     *      - line_base_price
     *      - line_base_price_formatted
     *      - line_sale_price
     *      - line_sale_price_formatted
     *      - line_tax_details: array of tax label => tax amount for this line (the tax amount for one unit x quantity)
     *
     * - cart_total_weight: in kg
     * - cart_total_quantity
     * - cart_total_tax_excluded
     * - cart_total_tax_excluded_formatted
     * - cart_total_tax_included
     * - cart_total_tax_included_formatted
     * - cart_discount_amount
     * - cart_discount_amount_formatted
     * - cart_tax_amount
     * - cart_tax_amount_formatted
     * - cart_tax_distribution: the tax of the items of this cart, grouped by tax.
     *      It's an array of tax_label => item, each item:
     *          - tax_value
     *          - amount: sum of tax amounts of the cart items bound to this tax
     *          - amount_formatted
     *
     *
     * - carrier_id: null|int
     * - carrier_label:
     * - carrier_estimated_delivery_date: text
     * - carrier_has_error: whether or not the carrier can deliver/not deliver the items.
     * - carrier_error_code: an error code issued by the carrier, indicating why it couldn't deliver the items
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
     *
     *
     *
     *
     * - has_coupons: bool
     * - coupons_total: sum of all coupons.saving_amount
     * - coupons_total_formatted
     * - coupons: array of applied coupons, each of which: @todo-ling, need to revisit this part...
     *      - label
     *      - code
     *      - saving_amount: the amount saved by this coupon
     *      - saving_amount_formatted
     *      - target: @todo-ling, should be something like:
     *              - product
     *              - cart_total
     *              - order_total
     *              - seller:$sellerName @see EkomModels::couponDetailsItem() for portability
     *              - shipping_cost
     *              - ...
     *      - type: @todo-ling: indicate the type of math operation to apply
     *      - value: the variable to use with type to apply the discount
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
     *
     *
     *
     *
     *
     *
     *
     * OLD DEPRECATED
     * ---------------------
     * The cart model comes in two forms:
     * - regular (by default)
     * - noGroups
     *
     * The noGroups form doesn't contain the itemsGroupedBySeller key.
     *
     * Currently in Ekom, the following objects are using the cartModel:
     * - CartLayer: uses the regular form
     * - CartModelEntity: uses the noGroups form
     *
     *
     *
     *
     *
     * - cart_total_quantity
     * - cartTotalWeight
     * - cartTaxAmount
     * - cartTaxAmountRaw
     *
     * - couponDetails, array of <couponDetailsItem> -- @see EkomModels::couponDetailsItem()
     * - couponHasCoupons
     * - couponSavingRaw
     * - couponSaving
     *
     *
     * - items
     *      - attributes
     *      - attributesSelection, array of selected attribute items, each of which:
     *          - attribute_id
     *          - attribute_label
     *          - name
     *          - value
     *          - value_id
     *          - value_label
     *      - attributesString
     *      - card_id
     *      - card_slug
     *      - cartToken
     *      - codes
     *      - defaultImage
     *      - description
     *      - discount: (see discountItem in this class)
     *      - ?discountBadge: only if discount is not empty
     *      - discountHasDiscount
     *      - discountLabel
     *      - discountPrice
     *      - discountRawPrice
     *      - discountRawSavingFixed
     *      - discountSavingFixed
     *      - discountSavingPercent
     *      - discountType
     *      - hasNovelty
     *      - imageLarge
     *      - imageMedium
     *      - imageSmall
     *      - imageThumb
     *      - images
     *      - label
     *      - label_escaped
     *      - metaDescription
     *      - metaKeywords
     *      - metaTitle
     *      - outOfStockText
     *      - priceBase
     *      - priceBaseRaw
     *      - priceLine
     *      - priceLineRaw
     *      - priceLineWithoutTax
     *      - priceLineWithoutTaxRaw
     *      - priceOriginal
     *      - priceOriginalRaw
     *      - priceSale
     *      - priceSaleRaw
     *      - productDetails
     *      - productDetailsArgs
     *      - productDetailsMap
     *      - productDetailsSelection
     *          - name
     *          - attribute_label
     *          - value
     *          - value_label
     *      - product_id
     *      - product_reference
     *      - product_type
     *      - quantityCart
     *      - quantityInStock
     *      - quantityStock
     *      - rating_amount
     *      - rating_nbVotes
     *      - ref
     *      - seller
     *      - taxAmount
     *      - @depr taxDetails
     *      - taxGroup
     *      - taxGroupLabel
     *      - taxGroupName
     *      - taxHasTax
     *      - taxRatio
     *      - uriCard
     *      - uriCardAjax
     *      - uriLogin
     *      - uriProduct
     *      - uriProductInstance
     *      - uri_card_with_details
     *      - video_info
     *      - weight
     *
     *
     * - itemsGroupedBySeller  (only regular form)(see CartUtil), array of seller => item, each item:
     *      - taxHint: int, a number indicating
     *                       the type of visual hint to display next to the price totals for every seller.
     *                       Whether or not the tax was globally applied.
     *
     *      - total: the total to display
     *      - totalRaw: the internal total used for computation
     *      - taxAmountTotal: the total amount of tax for this seller
     *      - taxAmountTotalRaw: the internal total of tax for this seller
     *      - taxDetails: an array, each entry representing a tax group applied to at least one product for this seller.
     *                   Each entry is an array of taxGroupName to item, each item being an array with the following structure:
     *                   - taxGroupLabel: string, the tax group label
     *                   - taxAmountTotalRaw: number, the cumulated amount coming from this tax group for this seller
     *                   - taxAmountTotal: the formatted version of taxAmountTotalRaw
     *
     *      - items: the items for the current seller
     *
     *
     * - priceCartTotal
     * - priceCartTotalRaw
     * - priceCartTotalWithoutTax
     * - priceCartTotalWithoutTaxRaw
     *
     * - priceOrderTotal
     * - priceOrderTotalRaw
     * - priceOrderGrandTotal
     * - priceOrderGrandTotalRaw
     *
     *
     *
     * - shippingDetails: empty array, or:
     *      - estimated_delivery_text: null|string
     *      - estimated_delivery_date: null|datetime|[datetimeStart, datetimeEnd]
     *      - label: label of the carrier
     *      - carrier_id: id of the carrier used
     *
     * - shippingErrorCode: string|null, an error code that prevents the checkout process to complete. It's application specific.
     * - shippingIsApplied: bool, whether the shipping cost currently applies to the cart amount
     * - shippingShippingCost
     * - shippingShippingCostRaw
     * - shippingShippingCostWithoutTax
     * - shippingShippingCostWithoutTaxRaw
     * - shippingTaxAmount
     * - shippingTaxAmountRaw
     * - shippingTaxDetails
     * - shippingTaxGroupLabel
     * - shippingTaxGroupName
     * - shippingTaxHasTax
     * - shippingTaxRatio
     *
     *
     * Note: assuming both estimated delivery date and delivery text are provided, the template will decide which
     * one to display.
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
     * couponDetailsItem
     * ===============
     * - code: coupon code
     * - label: coupon label
     * - savingRaw: the unformatted amount of saving for the ensemble of the discounts for this coupon
     * - saving: the formatted version of savingRaw
     * - target: string, 256 chars.
     *              Can be used in any way you want.
     *              However, ekom by default uses the following heuristics:
     *
     *              - <emptyString>
     *                      distribute the coupon amount equally amongst all sellers.
     *                      Note: we could also use a ratio proportional to the
     *                      amount of the order handled by a seller,
     *                      but as for now, this technique is not implemented.
     *
     *              - seller:$sellerName
     *                      apply the coupon amount only to the $sellerName seller
     *
     *
     * - details: array, free form
     */
    private function couponDetailsItem()
    {

    }


    /**
     * couponInfo
     * ================
     * - code: string, the code of the coupon
     * - active: 1|0, whether or not the coupon code is considered active when added to the coupon bag
     * - procedure_type: string (see discountItem in this class)
     * - procedure_operand: string (see discountItem in this class)
     * - target: string, the target of the coupon (see database.md for more info)
     * - label: string, the coupon label
     *
     *
     */
    private function couponInfo()
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
     */
    private function miniBoxModel()
    {

    }


    /**
     * orderDataModel
     * -------------
     * - user_id
     * - shop_id
     * - lang_id
     * - currency_id
     * - ?carrier_id              (only if at least an item needs to be shipped)
     * - ?shipping_address_id     (only if at least an item needs to be shipped)
     * - ?shop_address_id         (only if the shipping_address_id is defined)
     * - billing_address_id
     * - payment_method_id
     * - ...your own
     *
     */
    private function orderDataModel()
    {

    }


    /**
     * orderModel
     * ====================
     *
     *
     * - shop_id: int
     * - user_id: int
     * - reference: string
     * - date: datetime
     * - amount: number, total amount of the order
     * - coupon_saving: number, total amount of coupon for this order
     * - cart_quantity:
     * - currency_iso_code:
     * - lang_iso_code:
     * - payment_method: string
     * - payment_method_extra: string
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
     * - shop_info:
     * ----- id:
     * ----- label:
     * ----- host:
     * ----- lang_id:
     * ----- currency_id:
     * ----- currency_iso_code:
     * ----- currency_exchange_rate:
     * ----- timezone_id:
     * ----- timezone:
     * ----- address: <shopPhysicalAddress> -- @see EkomModels::shopPhysicalAddress
     *
     * - shipping_address: <addressModel> | false (false if shipping address doesn't apply) -- @see EkomModels::addressModel()
     * - billing_address: <addressModel> -- @see EkomModels::addressModel()
     *
     *
     * - order_details:
     * ----- cartModel without itemsGroupedBySeller -- @see EkomModels::cartModel()
     * ----- payment_method_id: the payment method id
     * ----- payment_method_name: the payment method name
     * ----- payment_method_label: the payment method label
     * ----- payment_method_details: array, depends on the chosen payment method handler
     * ----- ?carrier_id: (only if a carrier was used)
     * ----- ?carrier_name:
     * ----- ?carrier_details: array
     * ----- ?shipping_comment: string
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
     *
     *
     * - attributes: array of attrName => attrItem:
     * ----- attrName:
     * --------- label: string, the attribute label
     * --------- values: array of attributeValueItem:
     * ------------- 0:
     * ----------------- value: string, the attribute value
     * ----------------- value_label: string, the attribute value label
     * ----------------- value_id: string, the attribute value id
     * ----------------- selected: string (0|1)
     * ----------------- productUri: string, the product uri
     * ----------------- getProductInfoAjaxUri: string, the uri used to refresh the product via ajax
     * ----------------- product_id: int
     *
     * - attributesSelection: array of selected attribute item:
     * ----- 0:
     * --------- attribute_id: string
     * --------- attribute_label: string
     * --------- name: string
     * --------- value: string
     * --------- value_id: string
     * --------- value_label: string
     *
     * - attributesString
     * - card_id
     * - card_slug
     * - codes
     * - defaultImage
     * - description
     *
     * - discount
     * - discountBadge  (pc20)
     * - discountLabel
     * - discountHasDiscount
     * - discountPrice
     * - discountRawPrice
     * - discountRawSavingFixed
     * - discountSavingFixed
     * - discountSavingPercent
     * - discountType
     *
     * - hasNovelty
     * - imageLarge
     * - imageMedium
     * - imageSmall
     * - imageThumb
     * - images
     * - label
     * - label_flat
     * - metaDescription
     * - metaKeywords
     * - metaTitle
     * - outOfStockText
     * - popularity
     * - priceBase
     * - priceBaseRaw
     * - priceOriginal
     * - priceOriginalRaw
     * - priceSale
     * - priceSaleRaw
     *
     * - productBoxContext      // array for dev
     *
     * - @depr productDetails
     *          The product details array (major/minor), created by modules
     * - productDetailsArgs
     *              product details passed via the uri
     * - productDetailsMap,
     *              all product details identifying the product,
     *              this is the recommended form (of product details) to
     *              use to identify a product
     * - productDetailsSelection, array of item:
     *      - name
     *      - attribute_label
     *      - value
     *      - value_label
     * - product_id
     * - product_reference
     * - product_type
     * - product_type_id
     * - quantityInStock
     * - quantityStock
     * - rating_amount
     * - rating_nbVotes
     * - ref
     * - seller
     *
     * - taxAmount
     * - taxGroupLabel
     * - taxGroupName
     * - taxHasTax
     * - taxRatio
     *
     * - uriCard
     * - uriCardAjax
     * - uriLogin
     * - uriProductInstance
     * - video_info
     * - weight
     */
    private function productBoxModel()
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
     *
     * It has two forms:
     *
     * form1 (typical case)
     * ---------
     * - @depr estimated_delivery_text: null|string
     * - estimated_delivery_date: null|datetime|[datetimeStart, datetimeEnd]
     * - shipping_cost: number, the cost of the shipping of the accepted products (without tax applied)
     * - @depr ?tracking_number: string
     *
     *
     * Note: assuming both estimated delivery date and delivery text are provided, the template will decide which
     * one to display.
     *
     *
     * form2 (erroneous)
     * -----------
     * The carrier, for some reasons, will not handle the order.
     *
     * - errorCode: string, an error code that prevents the checkout process to complete. It's application specific.
     *
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
     * - id
     * - first_name: not used for a company
     * - last_name: use this field to put the company name
     * - phone
     * - address
     * - city
     * - postcode
     * - supplement
     * - active
     * - country
     * - country_iso_code: the country iso code
     * - country: the country label
     *
     *
     */
    private function shopPhysicalAddress()
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
     * - ek_user.*
     * - ek_user_group.name as group_name
     */
    private function userInfoModel()
    {

    }
}