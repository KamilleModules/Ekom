Refuting the idea of having the sale price in the database
=========================================================
2017-11-06


Here is my diagnostic: 


- you want to display a list of products
- each product has potentially a discount attached to it, which amount is predictable...
- ... but, whether or not the discount applies potentially depends on dynamic variables, such as the date,
        the user group(s), the currency chosen by the user, the lang of the shop, ...
        
        Similarly, the same problem applies to taxes, since whether or not the tax applies can depend on things
        such as the user country, the user shipping address, etc...       
- therefore the sale price depends on many factors: there is not just one possible sale price, but many.
        And so to display the list, you must run through each product and resolve the discount conditions
        first (and taxes too) in order to obtain the sale price of the product.
        
- in the end, this means that you can't use a field in the database to cache the sale price (it just doesn't make sense)        
        An auxiliary consequence of this is that the sql request fetching products (in a list) can't order them
        by sale price: you need to find another system
        
        
