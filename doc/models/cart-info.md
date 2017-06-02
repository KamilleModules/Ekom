CartInfo
============
2017-05-29



- cartInfo:
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
            - displayPrice: float, the (formatted) price to display, based on ekom modules internal rules 
            - displayPriceDiscount: float, the (formatted) price to display, based on ekom modules internal rules 
            - displayPriceUnformatted: float, the unformatted price (used for internal computation, not meant to be used by templates)  
            - priceWithoutTax: float, the original price (without tax)
            - priceWithoutTaxUnformatted: the unformatted version of the price without tax (not intended to be displayed)
            - priceWithTax: float, the price with taxes
            - priceWithTaxUnformatted: the unformatted version of the price with tax (not intended to be displayed)
            - image: str, the main image uri
    - totalWithoutTax: string: the formatted total without taxes applied, and without (estimated?) shipping costs
                            (priceWithoutTax x quantity)
    - totalWithTax: string: the formatted total with taxes applied, and without (estimated?) shipping costs
                            (priceWithTax x quantity)
    - displayTotal: string: the formatted total chosen by ekom as the recommended total price to display
            
