Ekom product box context
===========================
2017-11-06




The **ekom product box context** is the array representing the information necessary to display a product.

In other words, it's the ensemble of arguments you need to pass to a function to return a unique product.



It is composed of two elements:

- ekom product general context
    This one is created by modules, it represents data not directly related to the product, such as:
            - date
            - user related date (shipping address, country, group, ...)
            - ...
            
- ekom product specific context
    This one is created by ekom and is invariable, it represents the data directly related to the product:
            - product_card_id: int
            - product_id: null|int, the representative product of the card (or otherwise the default representative 
                    will be chosen) 
            - product_details: array of potential product details
            - shop_id: int|null, null to let ekom use the current shop_id
            - lang_id: int|null, null to let ekom use the current lang_id
    

        
        
                