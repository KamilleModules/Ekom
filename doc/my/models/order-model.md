Order model
============
2017-08-07


The current state of order model looks like this.

(note: it will need to be redone: too messy)


- checkoutMode: singleAddress
- isB2B: bool
- billingAddress: <addressModel> 
- shippingAddress: <addressModel> 
- shippingAddresses: <addressModel[]> 
- selectedShippingAddressId: int 
- defaultCountry: (id) 
- shippingAddressFormModel: OnTheFlyForm => Ekom.UserAddress
- useSingleCarrier: bool
- paymentMethodBlocks: array of id (ek_payment_method) => :
       - label
       - type
       - ?panel
       - is_preferred
       
- orderSectionSubtotalWithoutTax: 
- rawOrderSectionSubtotalWithoutTax: 
- orderSectionTotalWithoutTax: 
- rawOrderSectionTotalWithoutTax: 
- orderGrandTotalWithoutTax: 
- rawOrderGrandTotalWithoutTax: 
- orderSectionSubtotalWithTax: 
- rawOrderSectionSubtotalWithTax: 
- orderSectionTotalWithTax: 
- rawOrderSectionTotalWithTax: 
- orderGrandTotalWithTax: 
- rawOrderGrandTotalWithTax: 
- beforeShippingCoupons: 
- afterShippingCouponDetails: 
- couponTotalSavingWithoutTax: 
- rawCouponTotalSavingWithoutTax: 
- couponTotalSavingWithTax: 
- rawCouponTotalSavingWithTax: 

- paymentMethodId: 
- paymentMethodOptions: null
- paymentMethod:
    - label 
    - type 
    - img 
    - ...depends on the options chosen by the user 
- orderSections:
    - rawTotalShippingCost
    - totalShippingCost
    - sections: 
            <cartModel.items>
            or
            - name:
            - shippingCost:
            - rawShippingCost:
            - estimatedDeliveryDate:
            - carrierLabel:
            - productsInfo: <cartModel.items>
            - trackingNumber:
            
    - notHandled: <cartModel.items>

- taxAmount: (cartModel)
- rawTaxAmount: (cartModel)
- linesTotal: (cartModel)
- rawLinesTotal: (cartModel)
- linesTotalWithoutTax: (cartModel)
- rawLinesTotalWithoutTax: (cartModel)
- linesTotalWithTax: (cartModel)
- rawLinesTotalWithTax: (cartModel)
- cartTotal: (cartModel)
- rawCartTotal: (cartModel)
- cartTotalWithTax: (cartModel)
- rawCartTotalWithTax: (cartModel)
- cartTotalWithoutTax: (cartModel)
- rawCartTotalWithoutTax: (cartModel)

- orderSectionSubtotal: 
- rawOrderSectionSubtotal: 
- orderSectionTotal: 
- rawOrderSectionTotal: 
- orderGrandTotal: 
- rawOrderGrandTotal: 
- couponTotalSaving: 
- rawCouponTotalSaving: 





