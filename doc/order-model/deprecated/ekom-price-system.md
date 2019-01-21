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


In ekom, we start from the shop owner, and then the customer.

The shop owner will create a product and choose a price for it.

It is likely that this product will have taxes applied to it.

If this is the case, the shop owner ends up with two prices versions for the same product: 
a price without tax, and a price with tax.
 
So the first question might be: which version of the price should the customer pay? 
 
 
A shop owner also has a business strategy, such as b2b or b2c, and in ekom the price that the customer pays
depends on that strategy.


In b2b, customer pay the price without taxes.

In b2c, customer pay the price with taxes.

A website can mix both strategies, depending on whether or not the customer (aka user) is connected to her account for instance.


So indeed we see how the price can potentially split a website in two independent sub-websites, each having its own prices.
 
In ekom, which type of price is displayed is governed by the priceMode directive.
 
 
priceMode depends on a shop and a user, which means that for a given shop and user, the priceMode is always the same.
The formal definition of priceMode being: the priceMode defines whether the user pays the price without tax or the price with tax.
 
 
That being said, even if you are going to pay the price with tax, sometimes you have an indication mentioning the price without tax.

For this reason amongst others, the price model always contain both versions of all prices states (we are going to explain price state
just after).


Price state?

Every time the price is affected, it gives us a new price state.

A price state is symbolic, however it concretely resolves to one of the two versions: the version without tax or the version with tax.
 
 
Price states
----------------
The different price states in ekom are the following:
 
 
- price: the original price set by the shop owner
- sale price: the price with discounts applied to it
- line price: the price multiplied by the purchased quantity
- lines total: the sum of all "line price"s 
- cart total: the lines total with cart discounts applied to it
- order section subtotal: the cart total with shipping costs applied to it
- order section total: the order section subtotal with order discounts applied to it
- order section grand total: the sum of "order section total"s 
 


Here is a figure: [![ekom-order-model3.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model3.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model3.jpg)

The order section is a cart products' organizational unit based on an user shipping address and a carrier.
This basically takes into account the fact that a user can send one order's products to multiple addresses,
and using various carriers.






### Example

Let's illustrate the price states in a virtual example.
 
The shop owner creates product AA with a cost of 100€.
The tax for this product is 50%.


So, we have:

- priceWithoutTax: 100€
- priceWithTax: 150€


Now she sets the priceMode so that users with no group (including non connected users) will pay the price with tax,
and users in group B2B will pay the price without tax.


Now the shop owner applies a discount.
The discount is applied either on the price without tax or the price with tax (that's the target).

The shop owner sets a discount of "-10€" for users with no group (including non connected users), 
using a target of priceWithTax (since those users will pay the price with tax).

Plus, she sets another discount of "-15€" for users of group B2B, using a target of priceWithoutTax this time.

Note that a user of group B2B will only benefit the "-15€" discount (that's how it works in ekom with groups).

That's it for the shop owner side; now let's switch to the customer side.
The customer Alice buys the product AA and put it in her cart.

Since she is not connected, the price is the price with tax, minus the no group discount: 150-10 = 140€.

Now she connects, and the price changes as she belongs to the B2B group: the price is now 
the price without tax, minus the B2B discount: 100-15=85€.


















