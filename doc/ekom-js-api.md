Ekom js api
===================
2017-05-23


The goal is to make it possible for widgets to communicate with other widgets on the same page.


Use case: on the product page, the widget named productBox provides an "addToCart" button.
When this button is clicked, we would like the cart icon in the top bar to be incremented, so that the user
knows that her cart is not empty.

The problem is that the cart icon is displayed by another widget called TopBarWidget (for instance).

So, how can this click be intercepted?

Meet the ekom js api.






The basic principles
=====================


A widget can call the ekom js api any time it wants.


```js
var api = ekomApi.inst();
```

The ekomApi will always be loaded at the end of the page (just before the body end tag).
This means a widget call must be written AFTER that.

You should use the **A::addBodyEndJsCode**, like this:

```php
A::addBodyEndJsCode("plainJs", <<<EEE
 // your js code
EEE
);


// or
A::addBodyEndJsCode("plainJs", file_get_contents("my-init.js"));

```


The methods
================

Ekom js api is mostly based on the event listener principles: 
there are some events, and you can listen to them.

The widget authors use their common sense to implement their part (whether they should trigger an event or
listen to an event...). 


The following events are available:

- addToCartAfter: the addToCart button has been clicked, and the new cart info is available via ekomApi.getCartInfo
- getCartInfo: returns an array containing the cart info:
    - totalQuantity: sum of items.quantity
    - items: array of items, each item being an array with the following elements:
            - product_id: int, the id of the product
            - label: str, the label of the product
            - ref: str
            - uri: the uri of the product 
            - delete_uri: the uri to call to delete the product  
            - update_qty_uri: the uri to call to update the quantity of the product, you must append the equal symbol followed by the new quantity to that uri,
                                    so, the full uri looks like this: $update_qty_uri=2.
                                    If the quantity is zero, then it will have the same effect as to delete the product
                                    
            - uri_card: the uri of the product card 
            - quantity: int
            - product_card_id: int, the id of the product cart
            - attributes: array of attribute, each attribute is an array containing:
                    - attribute_id:
                    - label: the (translated) name of the attribute
                    - value: the value of the attribute
            - discount_price: null|float, if not null the discount price  
            - price: float, the original price
            - image: str, the main image uri
    - total_without_tax: float: the total without taxes applied
    - total_with_tax: float: the total with taxes applied
    



To subscribe to an event, we can use the **on** method of the ekomApi, like this:

```js
ekomApi.on("addToCart", function(){
    // my code
});

```







        
        
        
