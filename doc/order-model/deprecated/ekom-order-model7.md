Ekom price system 7
=====================
2017-06-26


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-price-system.md**,  **ekom-price-system2.md**, **ekom-order-model6.md** and **ekom-coin-model.md**, which at the time of writing are accurate.


How the price is affected is an essential question in e-commerce.

In ekom, it depends on many actors, including:

- taxes
- discounts
- coupons (aka cart discounts)
- shipping costs



I figured out that the same concrete reality can be interpreted in many different ways,
which leads to multiple implementations, and therefore I believe the way of thinking of a model is 
the most important thing to understand the implementation.

That's why I want to explain the ekom approach, which might be wrong or right, but accurate for ekom developers.




How is the price affected?
===========

The major change in this revision is that each price state is declined in three versions: 

- symbolic version
- without tax version
- with tax version

Here is the model used by ekom: [![ekom-order-model7.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model7.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model7.jpg)




How does it work?
-------

The shop owner sets the price of her product.

Then she set up the discounts, which gives us the discountedPrice.

Then, we apply the taxes, which gives us the discountedPriceWithTax.

From there, we will always have three distinct versions of the price:

- the symbolic version
- the without tax version
- the with tax version

The symbolic version is just an alias to either the without tax version or the tax version,
depending on the business type (b2b or b2c).
Templates generally use the symbolic version as it comes with less overhead.


The salePrice is an alias for the discountedPrice.
 
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
- salePrice 
- (multiply by quantity) 
- linePrice 
- (sum all linePrice) 
- linesTotal
- (apply "before shipping" cart discounts) 
- cartTotal
- (add shipping costs) 
- orderSectionSubtotal 
- (apply "after shipping" cart discounts)
- orderSectionTotal
- (sum all orderSectionTotal) 
- orderGrandTotal






