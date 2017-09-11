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


