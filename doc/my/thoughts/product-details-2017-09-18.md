Product details
===================
2017-09-10


Product details is a very important part of ekom when you try to extend a product.

Product details basically allow us to circumvent the limitations of attributes.


There are two main ideas behind product details:

- it identifies a product (i.e. it's an extension of the attributes system)
- it's passed via the uri



Product details identifies the product
---------------------------------------

Product details are part of the product's identify, same as attributes being part of the product's identity,
so that if you add a product in your cart, the item recognition pattern only match if both the product id and the product details match.



So, for instance, if a cart contains the following:

```txt
- products:
    - 0:
        - id: 50
        - qty: 5
        - details: []
```
        
        
And now the user adds product #50 with other details, we end up with two different products in the cart:        

```txt
- products:
    - 0:
        - id: 50
        - qty: 5
        - details: []
    - 1:
        - id: 50
        - qty: 1
        - details: [
            - martial_art: judo,
        ]
```


But actually, it's a little bit more complicated than that.

Let's call product instance a product identified by a unique combination of details.

Since each product is unique, to remove them from the cart we need to have a unique identifier.
In ekom, we create such a unique identifier using a hash of the detailsParams (which is explained later).

So the real picture looks more like this:

```txt
- products:
    - 0:
        - id: 50
        - qty: 5
        - details: []
        - detailsParams: []
    - 1:
        - id: 50-hjj44r0t0t0tz0egoeoe
        - qty: 1
        - details: [
            - martial_art: judo,
        ]
        - detailsParams: [day => mar]
```


Notice that the hash is only added if the product contains detailsParams.

The details (not detailsParams) is an extra container to help with modules implementing their logic.


This means in order to display a product page, we need the product details, which are passed via the uri. 



So, that's the core idea #1 behind product details.





Product details are passed via the uri
---------------------------------------

Product details will be presented as options to the gui user.

The goal here is that when the user clicks a product details option, it remains active (for instance a red border appears around
the selected option).

In order to do so, we have many implementation choices: $_GET, $_POST, $_SESSION, but I believe that the best choice
is $_GET, because then we can fully control the product page from the uri: it's cleaner as in more transparent to the user.



Product details definition for implementors
---------------------------------------------

The product details is divided in two:

- product details
- product details params


The product details's params definition for implementors is:

- the product details params is the ensemble (array) of uri key/value pairs identifying a product instance, ordered by key asc.

This is used to compute the identity of the product.

The session cart's details is used to display the product details string (the human description identifying a particular
configured product instance).
It can be empty if the module can compute this information without further variables.





Implementation guidelines
===========================


[![product-details-implementation-memo.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/product-details-implementation-memo.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/product-details-implementation-memo.jpg)



As I'm creating the EkomEvents module, specific for my company's application,
I'm writing this memo as a guideline for my future self and friends, as to what steps need to be done in order
to implement a "complex" product.


Basically, follows the natural flow of things as if you were the customer.
 

- do the productBox (gui) first: the customer sees the product box page first
- then do the cart: the item that the customer put in the cart must appear correctly in the cart
- take care of the quantity problem (see below)




The product box page
-----------------------
- start with the product box (gui):
    - extend the ProductBoxRenderer (see EventProductBoxRenderer)
    - the only constraints are:
        - (recommendation) any option can be triggered via the uri
        - when you press the addToCart button, it adds the necessary details, use the following js code:
        
```js 
api.on('productBox.collectDetails', function (details) {
    //... extend details here as necessary
});
```        


Another trick you can use is the refresh-trigger trick (ProductBoxRenderer):


```php
// code from class-modules/EkomEvents/Api/Layer/EventLayer.php

$baseUri = UriUtil::getProductBoxBaseAjaxUri($productId);
$uri = UriTool::uri($baseUri, ['dy' => $tmpDays], false);

```

```html
<!-- when the user clicks a refresh trigger, the productBox api automatically refreshes the page  -->
<div class="refresh-trigger" data-ajax="<?php echo $uri; ?>"></div>
```



Alternately, you can use the **window.ekomRefreshProductBox** function provided by the ProductBoxRenderer.


```php
window.ekomRefreshProductBox(uri);
```




The cart
---------

Details in the cart should identify the product, same as attributes do.

This means that the same product (same product id) with different details IS ANOTHER PRODUCT.


To help visualize this idea, I introduce the concept of product identity, such as the identityString uniquely
identifies ANY product (module special products included) PURCHASED by a customer.

- identityString: <productId> (<-> <detailsHash>)?

With detailsHash: a (unique) hash representing the details.

The optional part of the identityString is only appended if the product use the details system.

 
So, in the session cart, we should have something like this:

```php
- 0:
----- id: string, product identity
----- qty: int, the quantity in the cart for this product
----- details: array containing the product details
- ...
``` 

I recommend that modules use namespaces to encapsulate their details:

```php
- 0:
----- id: string, product identity
----- qty: int, the quantity in the cart for this product
----- details: 
--------- myModule: array of details originating from the myModule module
--------- ...
- ...
```                 


This concept of identityString is very important for you to grasp, because if you've got it, 
you can resolve any problem.

So again, for every operation that relates to the cart, the identity string should be used,
which is basically the product id plus the option details that the user chose when putting the item in her cart.

Having all the product details is crucial for cart operations like updating the cart quantity for instance,
which requires the stock quantity (more on that below).





Then, to display the cart, modules should hook into cart rendering logic and use the details stored in the session cart,
using the cartModel's **productDetails** key.



Watch out for the quantity!!
-----------------------------

So, while implementing this first "complex" product, I found out that to override the default quantity 
with the current ekom system you had to modify code in multiple places.

Basically, I made a few improvements at the ekom level:

- ekom has now a built-in way of handling virtual quantities:
        it uses both properties of the box model:
            - quantity
            - cartQuantity
            - ?virtual_quantity (virtualQty?)
            
Basically, the idea is that if virtualQty is set, then use virtualQty,
otherwise, ekom computes the virtualQty using the following formulae:

- virtualQty = quantity - cartQuantity


Note: the virtualQty override was born to overpass the limitations of the formulae which doesn't necessary work
for all cases (like ekomEvents module "à la carte" courses system).            
            
            
!!! WHAT'S BELOW IN THIS SECTION MIGHT BE DEPRECATED...             
Apart from that, rather than trying to change the ekom core code to make it easier for implementors, this time I rather decided
to list the places where code needs to be changed, as I thought it was the best solution in this case for some reasons.

Anyway, the places are (might be deprecated, I don't know):


- Hooks::Ekom_Product_updateStockQuantity

    when you update the item quantity of your cart, the **CartLayer.updateItemQuantity** method
    calls the **ProductLayer.getProductQuantity** method to import the stock quantity in its codespace, and decide
    whether or not the update is possible.
    The **ProductLayer.getProductQuantity** method in turn calls the **Ekom_Product_updateStockQuantity** hook.


- Hooks::Ekom_decorateBoxModel

    when the productBox page is displayed, it needs to display a quantity.
    Using the **Ekom_decorateBoxModel** hook, you can override any property of the model,
    including the **quantity** property.
    This hook is called by the **ProductLayer.getProductBoxModelByCardId** method.



cartProductDetails
----------------------

Introducing two new properties to the cartModel:

- productCartDetails, helper array for modules and fed by modules,
    each module should put its configuration in a namespace inside this array.
        For instance, the ekomEvents module should create the productCartDetails.ekomEvents array
        and put all its configuration in it 
- productCartDetailsParams, array of url params, helps building the instance uri


