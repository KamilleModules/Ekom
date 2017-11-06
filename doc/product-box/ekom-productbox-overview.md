Ekom product box overview
=================
2017-11-04



[![ekom-productbox-overview.png](https://s19.postimg.org/jiixkqi6r/ekom-productbox-overview.png)](https://postimg.org/image/kkt43a0zz/)




The ekom product box contains all the information necessary to display a **product card**.

That is:

- the name of the product
- the description of the product
- the price
- the discounts applying to it
- the taxes applying to it
- the stock quantity available
- the available attributes 
- ...and more





The two systems
===================

Ekom productBox is composed of two layers, built on top of each other:

- the product attributes system
- the product details system


The product attributes system
----------------------------

The product attributes system is the native system provided by ekom.
It is based on tables provided by the ekom module:

- ek_product
- ek_product_lang
- ek_product_has_product_attribute
- ek_product_attribute
- ek_product_attribute_value
- ek_product_attribute_lang
- ek_product_attribute_value_lang


The product attributes system brings the concept of card on the table.
The concept of card is already explained in the **database.md** document.
Check it out for more details.


The product attributes system is simple but limited.
Hence the need for the product details system.



The product details system
-------------------------------
The product details system is described in greater length in a **product-details.md**-ish document.

The product details system was designed to circumvent the limitations of the product attributes system.

In particular, it allows modules to create any type of product, using their own heuristics and tables.

Often, modules that use time based attributes use this system (for instance if you sell events, you might
want to use the **product details system**).
