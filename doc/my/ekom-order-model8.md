Ekom price system 8
=====================
2017-08-24


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-price-system.md**,  **ekom-price-system2.md**, **ekom-order-model7.md** and **ekom-coin-model.md**, which at the time of writing are accurate.

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

Each price has three versions: 

- without tax version (with the "WithoutTax" suffix)
- with tax version (with the "WithTax" suffix)
- default version, which depends on whether or not the user is b2b or b2c (no suffix)




What are the price states?
-------

- price 
- (apply discounts) 
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






