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

So, that's the core idea #1 behind product details.





Product details are passed via the uri
---------------------------------------

Product details will be presented as options to the gui user.

The goal here is that when the user clicks a product details option, it remains active (for instance a red border appears around
the selected option).

In order to do so, we have many implementation choices: $_GET, $_POST, $_SESSION, but I believe that the best choice
is $_GET, because then we can fully control the product page from the uri: it's cleaner as in more transparent to the user.






Implementation guidelines
===========================

As I'm creating the EkomEvents module, specific for my company's application,
I'm writing this memo as a guideline for my future self and friends, as to what steps need to be done in order
to implement a "complex" product.


Basically, follows the natural flow of things as if you were the customer.
 

- do the productBox (gui) first: the customer sees the product box page first
- then do the cart: the item that the customer put in the cart must appear correctly in the cart




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



Then, to display the cart, modules should hook into cart rendering logic and use the details stored in the session cart,
using the cartModel's **productDetails** key.








