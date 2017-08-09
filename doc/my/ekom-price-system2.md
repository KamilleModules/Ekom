Ekom price system 2
=====================
2017-06-15


This document's intent in the long term is to supersede all other price related documents.

It's inspired by **ekom-price-system.md** and **ekom-order-model2.md** and **ekom-coin-model.md**, which at the time of writing are accurate.


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


In **ekom-price-system.md**, I was almost there, but I was struggling with this problem of price with tax or without tax,
plus the model was too rigid, meaning the implementation was mine, nothing else.
What if I'm wrong, what if I didn't take into account that in some country they do things differently?

This new system takes this into account, basically allowing for developers to hook at the different price states.

In that model, the without/with tax is just one hook.
The benefit of this is that we can apply that "without tax to with tax" update at any point of the price chain.


So, the price states are basically the same, but I also added the transform steps (within parenthesis).
Those transform steps are the base steps in ekom. 
You could add more transform steps if you needed to, or even remove transform steps (however I advice not removing transform steps unless you 
know exactly what you are doing).



It turns out we use two price chains:

- one for the cart items
- one for the order, starting at the linesTotal sum


I believe this system also plays well with the case of multiple addresses shipping. 


The chains' transformers are affected some order numbers, which helps ordering transformers in a multiple
module context.



EkomProductPriceChain: this chain can be used to display a product price
- price
- (applying tax if b2c): 100
- (applying discounts): 200
- salePrice


EkomCartPrice: this chain can be used to display a cart line
- salePrice
- (multiplying by quantity): 300
- linePrice



- (summing linePrices)



EkomTotalPriceChain: this chain is applied to a linesTotal and applies to an order section
- linesTotal
- (applying cart discounts with target: linesTotal)
- cartTotal
- (applying shipping)
- orderSectionSubtotal
- (applying cart discounts with target: orderSectionSubtotal)
- orderSectionTotal


(this is not a chain, just an addition)
- (summing ordersSectionTotal)
- orderGrandTotal


Another important fact about that system is that the price is "delivered" as a model (aka php array) which info
can be extended at each step, depending on your module.

For instance, the discount module includes the discount details in the model.



We still keep the priceLayer.getPriceMode to define dynamically whether or not
the price should be displayed with/without tax.


Note
=========
I made a quick implementation: here is a starting code to play with (have a look inside the EkomItemsPriceChain
class to see the logic):

```php

/**
 * @var $chainProduct EkomProductPriceChain
 */

/**
 * @var $chainCart EkomCartPriceChain
 */
/**
 * @var $chainTotal EkomTotalPriceChain
 */
$chainProduct = X::get("Ekom_getProductPriceChain");
$chainCart = X::get("Ekom_getCartPriceChain");
$chainTotal = X::get("Ekom_getTotalPriceChain");
$model = [];


//--------------------------------------------
// PRODUCT
//--------------------------------------------
$salePrice1 = $chainProduct->run(10, $model);
a($salePrice1, $model, $chainProduct->getHistory());



//--------------------------------------------
// LINES TOTAL
//--------------------------------------------
$linesTotal = 0;
$linesTotal += $chainCart->setQuantity(5)->run(12, $model);
$linesTotal += $chainCart->setQuantity(3)->run(5, $model);

a($linesTotal, $chainCart->getHistory(), $model);


//--------------------------------------------
// ORDER
//--------------------------------------------
$orderSectionTotal = $chainTotal->run(60, $model);
a($orderSectionTotal, $chainTotal->getHistory(), $model);


```




