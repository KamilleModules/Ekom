Product details and product configuration
===============================
2018-05-01



This document presents basically the same ideas that was presented in the product-details.md document,
but the concept of major/minor details has been replaced with product details (standing for old major
product details concept) and product configuration (standing for old minor details concept).



This is also a recap for me, as some time has passed and this concept is currently being re-implemented.



Product id, and the product attributes
--------------------

So, any combination of product attributes is bound to one unique product id.
That was the first idea in Ekom in terms of product "attributes", and luckily this concept is still up,
which makes it a solid idea in the real of e-commerce conception.



So, each product id has its own uri, so for instance:

- /product/kettle-bell-4kg
- /product/kettle-bell-6kg
- ...



Product reference id, and the product details
---------------------------------

Product details could potentially create new uris, but for now I've voted this idea down,
meaning that product details use the product's uri as the base, and details are added
as GET parameters (after the question mark in the uri).

This is because semantically, having product details as GET parameters sounds good, since they are just
details, and as such cannot "pretend" to have their own uris.

This concept of course could be changed, depending on the seo needs of the company using Ekom.

Each variation of product details leads to a new product reference (which is the new kid in the block
in Ekom). This allows to potentially set price/discounts on a per product-detail basis.

Good thing I believe.





Product configuration, the old minor product details concept redefined
------------------------

Currently in Ekom product configuration are parameters that don't affect the cart token.
Meaning, if you add an item to a cart. You can change the configuration and click the "add to cart" button again,
but you will not have two different items in your cart, only one item with quantity=2.

I've voted this idea down for the next implementation cycles to come.
That's because this prevents somebody to add two different items in her cart with two different configurations.
Rather, I believe we should add an "update cart" button, or even no button at all which is maybe the most logical/simple
idea (I maybe made a bad decision when I first implemented this part of old minor product details, because
I was the only gui user/beta tester, and for me it was convenient to be able to change the details quickly rather
than adding a new product. But now, I'm starting to be more attracted to the simplicity of letting the user
handle the problem herself, and so this idea disappear).


In the uri, product configuration will be in the GET params, exactly like product details (where else could they be?),
and they don't spawn their own uris (alike for product details), as they are JUST configuration details, so it doesn't
make much sense (in terms of seo, duplicate content...) to have an uri per-configuration variation.


In terms of price, product configuration don't create a new reference and therefore don't affect the price, and you
cannot affect a discount on a per-configuration-variation basis.

So, think about whether you should use product details or product configuration BEFORE hand, that's a good tip
for Ekom admins.





Product details vs product attributes, which one should I choose?
-------------------------------

There are sometimes multiple possible implementations to a same product.
Here are a few things to consider when you are about to choose the underlying mechanism of your product:

- with product details, you have more freedom about how details are generated, whereas with
        product attributes, you have to use the Ekom tables for that (ek_product_attribute and children).
        So you can create your own separated db schema (it's actually recommended) and store the data
        in a natural way.

        And so by extension of this idea, which is not obvious at first read, is that
        although Ekom has its own way of filtering a list of products per attributes,
        the filtering is not optimized for dates.

        That's because Ekom uses the "like" technique, but for date, a "between" technique
        would have been better, and Ekom currently doesn't differentiate between different
        attribute types as far as filtering is concerned.

        Displaying list of products being one of the major part in an e-commerce, it's quite important.

        With the product details, you can create your own db schema and create product having date columns.
        Then Ekom let you hook into its baseQuery (see the ProductQueryBuilderUtil utility, which is
        the base of all lists in Ekom),
        so that adding "between" filtering with your table's date columns is a breeze.

        So, in short, product details gives you more flexibility and power than product attributes.

        Product attributes works well only for products with "standard variations", like for instance
        a shoe that comes in red, blue, or green color, but you are limited (unless you hack Ekom)
        to a filtering of type "like" when a list is displayed on the front.









That's it
-----------

Ok, that's it.
Hopefully this reminder of the different types of products in Ekom was useful.

As of now, product configuration is not implemented, and I'm about to re-implement the product details
concept in terms of front gui (the backoffice was implemented successfully yesterday), armed
with my new (more general) vision of the Ekom project :)









Note: at the time of writing, the price column has been moved from ek_product to ek_product_reference.
This is how I want it to be, because it's simpler that way, in terms of conception mostly,
but also in terms of query, mind juggling etc...

One could argue that it would maybe be better to have a default price value on the ek_product table.
For now I've decided that it would bring too much complexity to the project and so I voted this idea down.
You can always use gui power to emulate a fallback price if that's what you are going for,
but it doesn't have to be part of the Ekom schema, which tries to be as epurated as possible and
just tries to represent/modelize the pure e-commerce "constraints"
(not the helpful add-ons that anyone could invent).

In other words, Ekom's schema philosophy is to be raw and simple, if you need whistles and bells,
add them yourself elsewhere.


