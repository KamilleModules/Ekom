Configurable item
=====================
2017-09-21


It's a product that we can configure.
It's best understood by representing the two areas where it makes a difference:

- the product box page
- the mini-cart


When the user changes details in the product box page, it updates the (available) quantity and the price on the page.


Adding a configurable item to the cart
----------------------------------------

There are two cases:

- the item is not yet in the cart.
        In this case, it's added to the cart like a regular (i.e. not configurable) product
- the item is already in the cart.
        In this case, the new item totally REPLACES the older one (i.e. both the quantity and the details)        




How do we create configurable products?
---------------------------------------

By default, a product is configurable if it uses minor details.
Every product that uses minor details is a configurable product by default.