Ekom price system
=====================
2017-06-13


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-order-model2.md** and **ekom-coin-model.md**, which at the time of writing are accurate.


How the price is affected is an essential question in e-commerce.

In ekom, it depends on many actors, including:

- taxes
- discounts
- coupons
- shipping costs



I figured out that the same concrete reality can be interpreted in many different ways,
which leads to multiple implementations, and therefore I believe the way of thinking of a model is 
the most important thing to understand the implementation.

That's why I want to explain the ekom approach, which might be wrong or right, but accurate for ekom developers.




How the price is affected?
===========


In ekom, we start with the shop owner and the product.
Then, the customer.

The shop owner creates a product, and she affects a price to the product.

That price is without tax.

Then, the shop owner creates taxes, which in ekom translates to creating tax groups,
and assign the products she wants to the tax groups.

So now, some of her products have a tax group applied to them.

So at this point we've got the price (price without tax), and the price with tax.

Then, our shop owner can create discounts.

Whether you apply the discount on the price with tax or without tax can make a difference (https://postimg.org/image/3kj63y76n/),
and so the shop owner has the choice of the target of the discount:
she can apply the discount to the price without tax or to the price with tax.

Also, the shop owner can affect multiple discounts to the same product.

Every time a discount is applied, we have two new versions of the price: the price without tax and the price without tax.


Let's try to visualize this.
It's basically the idea described in ekom coin model (https://postimg.org/image/5b275fopr/).

But for this document I would like to display the price as a wagon, transported from left to right, and being affected 
by different factors.

todo: figure










