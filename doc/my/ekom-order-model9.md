Ekom price system 9
=====================
2017-10-27


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-price-system.md**,  **ekom-price-system2.md**, **ekom-order-model8.md** and **ekom-coin-model.md**, which at the time of writing are accurate.

How the price is affected is an essential question in e-commerce.

In ekom, it depends on many actors, including:

- taxes
- discounts
- coupons (aka cart discounts)
- shipping costs




Reminder: what's an order section?
========================

An order is composed of order sections, each section encapsulating a carrier.
This is just in case the customer's products can't be handled by 
the same carrier, and so the order is distributed between different carriers.

I've seen this use case implemented in amazon, but in my company we don't need it,
at least for now, so, the idea is here in ekom, waiting to being implemented.
              



The chain of price states
======================= 
A price in ekom is always in one and only one state.

The possible states are the chain of price states, which is the following:

- originalPrice (or price)
    this is the price without tax
- salePrice
    this is the price with applicable taxes and discounts 
    (whether the discounts apply before/after the taxes 
    depends on the discount target, see the discount section 
    of the database-$date.md document for more details)
    





A price can only go down in the chain, and so the next state of a price is predictable.




From originalPrice to salePrice
------------------

We have the following schema, which is intended to help the implementors:


```txt


        Discount:           Discount: 
        beforeTax           afterTax
            |                 |
            |                 |
price -----------> tax ---------> salePrice


```





