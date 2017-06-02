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
                            
                            
                            
                            
              