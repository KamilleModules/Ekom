<?php


namespace Module\Ekom\Models;


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
     * cartModel (work in progress)
     * =================
     *
     * - cartTotalQuantity
     * - cartTotalWeight
     * - cartTaxAmount
     * - cartTaxAmountRaw
     *
     * - couponDetails, array of couponDetailsItem (defined in CouponLayer)
     * - couponHasCoupons
     * - couponSavingRaw
     * - couponSaving
     *
     *
     * - items
     *      - attributes
     *      - attributesSelection
     *      - attributesString
     *      - card_id
     *      - card_slug
     *      - cartToken
     *      - codes
     *      - defaultImage
     *      - description
     *      - discount
     *      - discountHasDiscount
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
     *      - taxAmountUnit
     *      - taxDetails
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
     * - itemsGroupedBySeller  (see CartUtil), array of seller => item, each item:
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
     *      - ?estimated_delivery_date
     *      - label: label of the carrier
     *      - carrier_id: id of the carrier used
     *
     * - shippingIsApplied: bool, whether the shipping cost currently applies to the cart amount
     * - shippingShippingCost
     * - shippingShippingCostRaw
     * - shippingTaxAmountUnit
     * - shippingTaxDetails
     * - shippingTaxGroupLabel
     * - shippingTaxGroupName
     * - shippingTaxHasTax
     * - shippingTaxRatio
     *
     *
     *
     *
     */
    private function cartModel()
    {
        return [];
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
     * orderModel
     * ====================
     *
     *
     * - user_id: int
     * - reference: string
     * - date: datetime
     * - pay_identifier: string
     * - tracking_number: string
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
     * ----- address: <shop>PhysicalAddress> -- @see EkomModels::shopPhysicalAddress
     *
     * - shipping_address: <addressModel> | false (false if shipping address doesn't apply) -- @see EkomModels::addressModel()
     * - billing_address: <addressModel> -- @see EkomModels::addressModel()
     *
     *
     * - order_details:
     * ----- cartModel without itemsGroupedBySeller -- @see EkomModels::cartModel()
     * ----- payment_method_id: the payment method id
     * ----- payment_method_details: array
     * ----- ?carrier_id:
     * ----- ?carrier_details: array
     */
    private function orderModel()
    {
        return [];
    }


    /**
     * shippingContextModel
     * =====================
     *
     * - cartItems: the cart items (cartModel.items)
     * @see EkomModels::cartModel()
     * @see CartLayer
     *
     * - cartWeight: number
     *
     * - shopAddress: null|array:shopPhysicalAddress
     * @see EkomModels::shopPhysicalAddress()
     *
     *
     * - shippingAddress: null|array:addressModel representing the user shipping address
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
     * - name: string, the name of the carrier
     * - label: string, the label of the carrier
     * - estimated_delivery_date: datetime|null, the estimated delivery datetime or null if it cannot be estimated
     * - shipping_cost: number, the cost of the shipping of the accepted products
     * - ?tracking_number: string
     */
    private function shippingInfoModel()
    {

    }

    /**
     * shopPhysicalAddress
     * =====================
     *
     * - id
     * - first_name
     * - last_name
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
}