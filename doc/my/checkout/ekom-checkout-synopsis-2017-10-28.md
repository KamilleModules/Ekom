Ekom Checkout synopsis
======================
2017-06-07



[![ekom-checkout-cheatsheet.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-checkout-cheatsheet.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-checkout-cheatsheet.jpg)



In ekom, we have different levels of information that allow a customer to purchase products on the website.

The levels are the following:


- cart
- shippings
- order sections




Cart level
===============

The cart accompany the user while she is browsing the products, 
it represents the intent of the user to buy some products.
 
It contains the following information:

- product id
- product quantity



Shippings
=============

In ekom, the term shippings represent something very specific: it represents the user's vision of how she distributes
the cart items into shipping addresses (for downloadable products, this section doesn't apply).


Usually, users do only one shipping, meaning they send all products to one shipping address.

But imagine Sally has 4 products A and 2 products B in her cart.
Now she wants 1 product A to be shipped at address "6 wall street", and 3 products A and the 2 products B to be shipped
at her other address "800 bd churchill".

She will then do two "shippings", the first shipping will look like this:

- 1 x product A     --> 6 wall street
- 3 x product A     --> 800 bd churchill
- 2 x product B     --> 800 bd churchill



Order sections
================


Now the user has chosen her shippings preferences.
However, it might be the case that the carrier available to a shop will not be able to ship the products exactly
as the user wants.

If that's the case, we need to inform the user.

So we create "order sections" to check for what's possible to do.
An order section is basically the combination of a carrier and an address.

Usually, a carrier will be able to handle all of the user's shipping(s).

So for instance if we have two carriers: ups and postit,
if ups can handle every products, then we end up with the following order sections:

- ups - 6 wall street (order section 1)
    - estimated shipping date: 6 april 2017
    - shipping cost: 10€
    - products
        - 1 x product A
    
- ups - 800 bd churchill (order section 2)
    - estimated shipping date: 10 april 2017
    - shipping cost: 11€
    - products
        - 3 x product A    
        - 2 x product B    

    
Potentially, we might have to distribute the user's shippings accross multiple order sections.
That happens if for instance ups cannot handle the wall street address.
In that example, let's say postit can handle the wall street address.
If that's the case, our order sections would look like this:

- postit - 6 wall street (order section 1)
    - estimated shipping date: 6 april 2017
    - shipping cost: 10€
    - products
        - 1 x product A
    
- ups - 800 bd churchill (order section 2)
    - estimated shipping date: 10 april 2017
    - shipping cost: 11€
    - products
        - 3 x product A    
        - 2 x product B

If postit cannot handle the wall street line either, then we need to inform the user, so that she can remove the item
from her cart. 
Hopefully that last case doesn't happen too often, and it shouldn't since the shop owner usually choose carrier
that can handle the shipping of all products, but as developers we have to take this case into account.






Implementation details
==============================
2017-06-09 --> 2017-06-13


Like the cart, we store slim data in the session, and restore the corresponding model from cache.

We have different types of orders, the multiple address order as described above, or the single address order.

We probably could have more types.
 
As for now, since I have a very short deadline for this order implementation, I will only cover the order of type single address.
 
To acknowledge further order types, we simply use the type property, which can take any string
 
Here is our session model.


SingleAddress order model
----------------------------

- (ekom.)order.singleAddress

(the items are all the items in the cart, see ekom-cart for more details)

----- billing_address_id
----- shipping_address_id
----- carrier_id
----- ?carrier_options array of key => value, depending on the carrier (read relevant carrier doc for more info)
----- payment_method_id
----- ?payment_method_options: array of key => value, depending on the payment method (read relevant payment method doc for more info)







