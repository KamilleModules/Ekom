CartInfo
============
2017-05-29



- cartInfo:
    - displayPriceWithTax: boolean, whether or not (ekom suggested) to display the price with tax or without tax.
                        This might be useful only to display/not display the tax line subtotal.
                        See the ekom coin model figure related to display cart to see what I mean.
                        
    - totalQuantity: sum of items.quantity
    - items: array of items, each item being an array with the following elements:
            - product_id: int, the id of the product
            - stock_quantity: int, the quantity available for this product and this shop (shop_has_product.quantity)
            - quantity: int, how many of this product we have in the cart.
            - label: str, the label of the product
            - ref: str
            - weight: float, the weight of the product
            - uri: the uri of the product 
            - remove_uri: the uri to call to remove the product from the cart  
            - update_qty_uri: the uri to call to update the quantity of the product, you must append the equal symbol followed by the new quantity to that uri,
                                    so, the full uri looks like this: $update_qty_uri=2.
                                    If the quantity is zero, then it will have the same effect as removing the product from the cart
                                    
            - uri_card: the uri of the product card 
            - uri_card_with_ref: the uri of the product card, with the current ref selected 
            - product_card_id: int, the id of the product cart
            - attributes: array of attribute, each attribute is an array containing:
                    - attribute_id:
                    - label: the (translated) name of the attribute
                    - value: the value of the attribute  
            - attributeDetails: alias for attributes. I believe I had some bugs with json_encode and attributes,
                                so I personally use attributeDetails.
                                But your mileage may varyn if attributes work for you, you can use either keys. 
                                
            - attributeValues: array of attribute values, computed from attributes; template authors can implode this
            - originalPrice: float, the (formatted) price to display, based on ekom modules internal rules 
            
            - salePrice: string, the (formatted) price to display, based on ekom modules internal rules 
            - salePriceWithTax: string, the (formatted) price with tax to display 
            - salePriceWithoutTax: string, the (formatted) price without tax to display
            
            - linePrice: string, the (formatted) line price to display, based on ekom modules internal rules
            - linePriceWithTax: string, the (formatted) line price with tax to display 
            - linePriceWithoutTax: string, the (formatted) line price without tax to display
             
            - image: str, the main image uri, in thumb format (suited for mini cart)
            - imageSmall: str, the main image uri, in small format (suited for cart)
            - stockType: same as in product-box model
            - stockText: same as in product-box model
            
            // ----            
            - rawSalePriceWithoutTax: the unformatted version of the sale price without tax (not intended to be displayed)
            - rawSalePriceWithTax: the unformatted version of the sale price with tax (not intended to be displayed)
            - rawSalePrice: the unformatted version of the sale price, either with or without tax, based on ekom rules (not intended to be displayed)
            
    - totalWithoutTax: string: the formatted total without taxes applied, and without (estimated?) shipping costs
                            (priceWithoutTax x quantity).
                            Consider displaying this only if displayPriceWithTax is false.
                            
    - totalWithTax: string: the formatted total with taxes applied, and without (estimated?) shipping costs
                            (priceWithTax x quantity)
    - displayTotal: either the totalWithoutTax or the totalWithTax, depending on ekom internal rules
    - taxAmount: string: the formatted amount of taxes (totalWithTax - totalWithoutTax)
    
    - cartTotal: string, the (formatted) cart total (see ekom order model II for more details)
    - totalSaving: string, the negative formatted amount of saving made by coupons's cart discounts on the linesTotalWithTax target (see ekom order model II for more details).
    - hasCoupons: bool, whether or not this cart contains coupons
    - coupons: array containing the details of the coupon discounts applied to the cart
                        The array can have two forms: one if it's erroneous (i.e. an internal problem occurred), and one if it's successful.
                        The erroneous form has the following structure:
                            - error: 1
                            
                        The successful form is an array of couponDetail.
                        Each couponDetail is an array with the following structure:
                        
                            - code: string, the coupon code
                            - label: string, the coupon label
                            - saving: the negative formatted amount of saving for the ensemble of the discounts for this coupon
                            - discounts: array of $target => discountDetails
                                    Each discountDetail is an array with the following structure:
                                    - label: string, the discount label
                                    - old: float, just a reference to the price BEFORE the discount was applied
                                    - newPrice: string, the formatted price (AFTER the discount was applied)
                            
                        The $target can be one of the following values (see ekom order model II for more details):
                            - linesTotalWithTax
                            - linesTotalWithTaxAndShipping
    
    // work in progress...
    - carrierSections: array with the following structure:
            - sections: array of carrierName => sectionInfo, each sectionInfo is an array with the following structure:
                   - shippingCost: formatted shipping cost
                   - productsInfo: an array of product_id => productInfo
                           Each productInfo has the same structure as the passed productInfo.
            - notHandled: the array of not handled productInfo (productId => productInfo)
            - isEstimate: bool, whether or not the costs are just an estimate or the real shipping costs
            - totalShippingCost: string, formatted amount of shipping cost, sum of all sections' shipping costs.     
            
    - totalShippingCost: alias for carrierSections.totalShippingCost
    - orderGrandTotal: string, the formatted orderGrandTotal (see ekom order model II for more details)