Ekom price system 5
=====================
2017-06-25


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-price-system.md**,  **ekom-price-system2.md**, **ekom-order-model4.md** and **ekom-coin-model.md**, which at the time of writing are accurate.


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

The major change in this revision is that now b2b and b2c business types are unified.

Here is the model used by ekom: [![ekom-order-model5.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model5.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model5.jpg)

This is a simplification compared to the older model.
This simplification has a cost: it's not possible to apply discounts on the priceWithTax (at least ekom doesn't provide
an easy way to do so).



How does it work?
-------

The shop owner sets the price of her product.

Then discounts apply, which gives us the discountedPrice.

Then, we apply the taxes, which gives us the discountedPriceWithTax.

Depending on whether the current business type is b2b or b2c,
the user will pay the discountedPrice version or the discountedPriceWithTax version.
 
In ekom, this final price of the product is called the salePrice,
which is passed to the product box templates. 
 
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
- (choose discountedPrice if b2b or discountedPriceWithTax if b2c) 
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

