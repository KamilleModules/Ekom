Ekom Checkout synopsis
======================
2017-06-07



[![ekom-checkout-cheatsheet.jpg](https://s19.postimg.org/yl52v1wbn/ekom-checkout-cheatsheet.jpg)](https://postimg.org/image/x63i6bv8f/)



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






Information implementation
==============================

At the code level, the following structures can be used:

```txt
- shipping info
----- address
--------- product
--------- qty


- order section
----- carrier+address
--------- product
--------- qty
--------- estimated_date
--------- shipping_cost
--------- ...(other meta)
```











