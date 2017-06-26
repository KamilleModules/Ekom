Ekom price system 6
=====================
2017-06-25


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-price-system.md**,  **ekom-price-system2.md**, **ekom-order-model5.md** and **ekom-coin-model.md**, which at the time of writing are accurate.


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




How is the price affected?
===========

The major change in this revision is that now b2b and b2c disappear, and we have two price lines: one without
tax and the other with tax.

Here is the model used by ekom: [![ekom-order-model6.jpg](https://s19.postimg.org/wpiuecder/ekom-order-model6.jpg)](https://postimg.org/image/yu37fff1b/)


Note:
This is a simplification compared to the older model.
This simplification has a cost: it's not possible to apply discounts on the priceWithTax (at least ekom doesn't provide
an easy way to do so).



How does it work?
-------

The shop owner sets the price of her product.

Then discounts apply, which gives us the discountedPrice.

Then, we apply the taxes, which gives us the discountedPriceWithTax.

Now, we still aknowledge the fact that there are two main types of businesses: b2b and b2c.

In b2b, the user doesn't pay the taxes. 
In b2c, the user pays the taxes.

That being said, you need both the price with and without tax, in both business modes;
because for instance in b2b you want the price with tax just to have an idea of how much money you are saving
comparing to the price with tax, and conversely in b2c you might want to have the price without tax displayed
somewhere as an extra information.

Each price, and each transformation that occurs on the price then after has a two versions:
one without tax and one with tax.

 
 
You might have noticed that there is a salePrice on the schema, which is the only price to NOT have
a "with tax" variation.

Actually, it could have a "with tax" variation in the future, but for now it is meant as a symbolic
string for templates to symbolize the discounted price, no matter which business type is used (b2b or b2c).

 
Multiply the salePrice by the quantity and you get the linePrice.

Summing all line prices you get the linesTotal.

Applying "before shipping" cart discounts and you get the cartTotal.
 
Add the shipping costs and you get the orderSectionSubtotal.

Applying "after shipping" cart discounts and you get the orderSectionTotal.

Add all orderSectionTotal to obtain the final orderGrandTotal.


So this price system is composed of various price states in a given order:
 
- price 
- (apply discounts) 
- discountedPrice
- (apply taxes) 
- discountedPriceWithTax

- salePrice (contextualized alias for discounted price, with/without tax depending on the business type)
 
- (multiply by quantity) 
 
- linePrice / linePriceWithTax
- (sum all linePrice) 
- linesTotal / linesTotalWithTax
- (apply "before shipping" cart discounts) 
- cartTotal / cartTotalWithTax
- (add shipping costs) 
- orderSectionSubtotal / orderSectionSubtotalWithTax
- (apply "after shipping" cart discounts)
- orderSectionTotal / orderSectionTotalWithTax
- (sum all orderSectionTotal) 
- orderGrandTotal / orderGrandTotalWithTax






