Bundle
================
2017-11-09


A bundle is a group of products that you add to your cart altogether.

The rationale behind the bundle is that if the customer buys a lamp, she/he also might want to buy
the bulb that goes along with, and so the bundle is a quick way to add both (or more) in the cart in one click.





Related tables
-----------------
- ek_product_bundle
- ek_product_bundle_has_product



Discounts
-----------------

Before you apply a discount to a bundle: 

- do you want to keep the discounts on individual items in the bundle? 
- or do you want to remove the individual discounts?


### Possible Implementation 1

And so this question gives birth to a new product box context variable: isBundle (0|1), which equals 1 only
if we are inside a bundle, otherwise it's value is 0.

This variable, depending on an ekom configuration variable: bundleItemsLooseDiscount: bool=true,
is used by ekom to know whether or not to apply the discount.
 
 
### Possible Implementation 2
Rather than creating an extra product box context variable, we only use the ekom configuration variable:
bundleItemsLooseDiscount: bool=true.

Then in terms of implementation, we rely on the bundle layer to create the model with the basePrice or salePrice (depending
on the bundleItemsLooseDiscount value).
When the item is added to the cart, the cart "marks" each individual item as bundled (an extra bundleId property is created
with a unique bundle identifier created by the cart layer).

Plus, the cart also adds an extra property to keep the value of the bundleItemsLooseDiscount value (since the shop owner
could change this value without the customer being aware of it, creating potential unsync problems, which would 
have the dramatic effect of increasing/decreasing the price of the customer's cart items, which is obviously one of the
last thing we want to do).

Also, a bundle has discount table should be created to round all this.




We are currently using implementation 2.  
