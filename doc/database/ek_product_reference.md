ek product reference
=======================
2018-04-20




Why such a table?


This table encapsulates the price/quantity variation at the product details level.
(by product details, I only mean major product details, the minor product details concept is deprecated and will
be moved to a configurable product details concept later when we will implement it...)

- basically, it allows us to modelize the sentence: I want that this event XX on 2018-05-06 in Paris has a discount,
            whereas the same event XX but 2018-07-02 in Mexico doesn't.
            So we can set a different discount/price/quantity for each variation.
            
Each variation gets a unique reference, so that's easier for third party tools to interact with discounts/price/quantity
for a specific "product".


So with this new table in the game, we are forced to review our whole conception of the Ekom system.

In Ekom, we sell product references (not products).
The products are just info container for product references.



Benefits of having the ek_product_reference table include:

- the lists of products on the front search at the product details level,
        so for instance if we've applied a discount on unique reference XX123, 
        then if we click the "filter by discount" button we will see this reference appear in the results.
        
        
Drawbacks:

the system is not implemented yet (it's about to be), but I believe we will have to deal with the following drawback

- since the list of products will search at the product details level, then if I'm searching for an event of type AA,
I will have all event's variations appear in the list, which might/might not be exactly what I want...         





