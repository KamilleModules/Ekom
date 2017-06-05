Ekom order model II
==========================
2017-06-02



[![ekom-order-model2.jpg](https://s19.postimg.org/c5h38v6qr/ekom-order-model2.jpg)](https://postimg.org/image/l0hxjdvj3/)



There are two main ideas in ekom order model II:


- an order is composed of order sections, each section encapsulating a carrier.
            This is just in case the customer's products can't be handled by the same carrier,
            and so the order is distributed between different carriers.
              
- the cart discount rules can apply at any level (no restriction at all).
            In the figure, two target examples are shown:
                - linesTotalWithTax
                - linesTotalWithTaxAndShipping
                
            Those are the most common targets for cart discount rules.
                            
                            
                            
                            
General implementation guideline
=================================

The user enters a coupon code via the website.
The coupon is attached to a cart discount rule.

When the user is on the order page, her coupon(s) are applied to the different targets.

See the latest $date-database.md document for more information.
                            
                            

                            
How info is organized
==========================


- first we calculate the sale price,
        which is the price shown to the customer: it can be either without tax
        or with tax, but it takes the discount into account.
        Basically that's the base unit price that the customer will pay.
        
- then we multiply the sale price by the quantity, which gives us the line price
        
- for every product purchased, there is a corresponding line price.
          By adding all line prices together, we get the linesTotal.
          As said before, the linesTotal can be either with taxes or without taxes
          
- if the linesTotal is without taxes, then we now add the taxes,
          we end up with the linesTotalWithTax number.
          
- coupons generally apply to that linesTotalWithTax target,
          the result of the linesTotalWithTax minus the discount is called
          the cartTotal. 
          // todo: add the cartTotal entity to the figure.
          
- then, the shipping costs are applied, which gives us the order section total.

- if many order section are used (multiple shipping), then we add the 
        order section totals together and we get the orderGrandTotal,
        which is always the final price in ekom order model II.
        This means no cart rule can use the OrderGrandTotal as a target.
                            
              