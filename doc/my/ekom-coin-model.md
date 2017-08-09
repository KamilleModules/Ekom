Ekom coin model
===================
2017-06-02





While implementing the discounts system, I was having a hard time representing myself how things worked.
In fact, I told myself that a discount could apply to the price without tax or the price with tax,
but the relationship between the price, the tax and the discount was unclear.

So, after 2 days of struggling, I woke up this morning and the coin model came into my mind.

[![ekom-coin-model.jpg](https://s19.postimg.org/kjs4j7ieb/ekom-coin-model.jpg)](https://postimg.org/image/5b275fopr/)



The ekom coin model explained
====================

A product has a price, which is represented as a coin.

One side of the coin contains the price without tax (OT), and the other side contains the price with tax (WT).

So, this implies that OT and WT are always bound together (that's the key moment of this model).



Then, discount(s) apply.

We can have as many discounts as we want.
 
Computation of a discount is based on one side of the coin (chosen at the creation of the discount),
but as we've just seen, the discount will impact both sides anyway.


Note: if you wonder what's the relationship between OT and WT, we can switch from OT to WT with a simple 
multiplication, and from WT to OT with the reverse division.
 
 
The ensemble of the discounts is called discount chain, although it doesn't matter.
What's more important is that the ensemble of the original price plus the discount chain is called the chain.
 
 
 
The last node of the chain is the customer (base) price.
 
 
The customer price can then be displayed in order or carts views.
