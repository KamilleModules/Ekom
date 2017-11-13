Ekom product box context
===========================
2017-11-06




The **ekom product box context** is the array representing the information necessary to display a product.

In other words, it's the ensemble of arguments you need to pass to a function to return a unique product.


The primary goal behind the **product box context** is to display prices relevant to the current user profile.
Depending on different factors (is the user connected, what's her/his origin country, what's the current lang,
what's her/his shipping country...), the taxes and the discounts might be slightly different, thus affecting 
the price. 
By extracting those variables in the **product box context**, ekom is able to display a price that always matches
the user profile.







It is composed of two elements:

- ekom product general context
    This one is created by modules, it represents data not directly related to the product 
    (i.e. applying/common to every product), such as:
            - shop_id: int, the current shop_id (this is brought by ekom module)
            - lang_id: int, the current lang_id (this is brought by ekom module)
            - currency_id: int, current currency_id (this is brought by ekom module)
            - date
            - user related date (shipping address, country, group, ...)
            - ...
                       
            
- ekom product specific context
    This one is created by ekom and is invariable, it represents the data directly related to the product:
            - product_card_id: int
            - product_id: null|int, the representative product of the card (or otherwise the default representative 
                    will be chosen) 
            - product_details: array of potential product details
    

        
        
                