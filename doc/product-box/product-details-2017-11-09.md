Product details
===================
2017-09-19



Product details is an extension of the ekom attributes system.

Where attributes combinations give us unique product ids, product details allow us
to go even deeper and reflect more complex products.


Product details are such as if you pass the product details in the uri, 
the product page will reflect them no matter what.

(This feature is necessary so that a user can browse her purchased products history and land
on the desired product pages if necessary.)



We can divide product details in two groups:

- major product details
- minor product details


Major product details provoke a change of the token (see cart.md for more info about token).
Minor product details DO NOT provoke a change of the token.


In other words, not all product details change the product instance that you add in your cart.
Those minor product details are instead used to configure further a product instance.


This is what led us to distinguish two types of product instances (see cart again).


So to recap, if you are not sure whether your product details is major or minor, ask yourself this one question:

- if the product already exist in my cart, if I go to the product page and change a detail,
    does it increment the quantity in your cart, or does it just change the configuration of the existing
    product in your cart?
    
If the answer to this question is it increments the cart quantity, then it's a major product detail,
otherwise it's a minor product detail.



Put it differently, minor details override the default quantity system, they provide their own 
product quantities system. 

A product using minor details is called a configurable product.
    


If a module use the product details system, it must add the productDetails key in the product box model
(see the product box model for more info).


This property is required by the cart system to take a decision about whether to increment
or not increment the cart quantity when the "add to cart button" is clicked: the cart quantity of a 
configurable product is set --but not added to the previous quantity-- when the user clicks the add to 
cart button (see addItem method).





 