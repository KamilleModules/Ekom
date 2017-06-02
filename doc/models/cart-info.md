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
            - quantity: int, how many of this product we have in the cart
            - label: str, the label of the product
            - ref: str
            - uri: the uri of the product 
            - remove_uri: the uri to call to remove the product from the cart  
            - update_qty_uri: the uri to call to update the quantity of the product, you must append the equal symbol followed by the new quantity to that uri,
                                    so, the full uri looks like this: $update_qty_uri=2.
                                    If the quantity is zero, then it will have the same effect as removing the product from the cart
                                    
            - uri_card: the uri of the product card 
            - quantity: int
            - product_card_id: int, the id of the product cart
            - attributes: array of attribute, each attribute is an array containing:
                    - attribute_id:
                    - label: the (translated) name of the attribute
                    - value: the value of the attribute  
            - attributeValues: array of attribute values, computed from attributes; template authors can implode this
            - originalPrice: float, the (formatted) price to display, based on ekom modules internal rules 
            
            - salePrice: float, the (formatted) price to display, based on ekom modules internal rules 
            - salePriceWithTax: float, the (formatted) price with tax to display 
            - salePriceWithoutTax: float, the (formatted) price without tax to display, based on ekom modules internal rules
             
            - image: str, the main image uri
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
    - total: string: the formatted total chosen by ekom as the recommended total price to display
            
