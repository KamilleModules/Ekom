Ekom price system 3
=====================
2017-06-15


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-price-system.md**,  **ekom-price-system2.md**, **ekom-order-model2.md** and **ekom-coin-model.md**, which at the time of writing are accurate.


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

The major change in this revision is the fact that we clearly identify two parallel business models, which influences both
the concepts and the code.



There are two main types of businesses: b2b or b2c.

The differences are illustrated here: [![ekom-order-model4.jpg](https://s19.postimg.org/jqk92y38j/ekom-order-model4.jpg)](https://postimg.org/image/jdsuwrkyn/)




B2b
-------

b2b is the simplest system to understand, because there are no taxes involved.
The shop owner sets the price of her product.

Then discounts apply, which gives us the salePrice.

Multiply the salePrice by the quantity and you get the linePrice.

Summing all line prices you get the linesTotal.

Applying "before shipping" cart discounts and you get the cartTotal.
 
Add the shipping costs and you get the orderSectionSubtotal.

Applying "after shipping" cart discounts and you get the orderSectionTotal.

Add all orderSectionTotal to obtain the final orderGrandTotal.


So this price system is composed of various price states in a given order:
 
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


B2c
----------

b2c is almost the same, except that we use the price with tax as the basis for the calculations.
The tax are applied at the price level (very first state).

We have the following system:

- price 
- (apply taxes) 
- priceWithTax
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

 
 
 
 

