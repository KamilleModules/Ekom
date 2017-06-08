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

he widget authors use their common sense to implement their part (whether they should trigger an event or
listen to an event...). 


Here are the ekomJsApi public methods:



// events
- on ( eventName, cb ): adds the cb listener to event eventName,
            the args passed to cb are defined on a per-eventName basis.
            Use the trigger method to then trigger that event manually.
            See the events section to see all the available eventNames.
            
- trigger ( eventName, ?args...): triggers an event 


 
// cart
- cart.removeItem: (product_id ), remove an item from the cart
                    The following events are triggered:
                        - cart.updated
                         
- cart.addItem: (product_id, qty), add an item to the cart
                    The following events are triggered:
                        - cart.itemAdded
                        - cart.updated

- cart.updateItemQuantity: (product_id, newQty), update the quantity of an item.
                            If the item is not in the cart, it will be added.
                    The following events are triggered:
                        - cart.updated
                                                    
- cart.addCoupon: (code, force, onResponse), adds a coupon to the user's cart.

                    Use the onResponse callback to test the server's response.
                    
                    onResponse ( msg, type )
                            
                                with: 
                                    - type: error|success|confirm
                                    - msg: mixed
                                    
                    If the type is success, then msg is a string representing the 
                    success message to display.
                    
                    If the type is error, then msg is an array of errors that occured 
                    for trying adding the coupon.
                    
                    If the type is confirm, and the force flag is false, 
                    then msg is the confirm message to prompt to the user.
                    This happen when the coupon the user is trying to add is of type
                    unique and will therefore replace other coupons, which might not be
                    what the user want.
                    If the force flag is set to true, the confirm system is overridden
                    and there is only two possible outcomes: success or error (not confirm).
                                                                         
                                    
                    If adding the coupon is a success, then the cart.updated event is triggered.
                    If adding the coupon is not a success, no event is triggered.
                    
                    The following events are triggered:
                        - cart.updated  (only in case of success)
                                                    
- cart.removeCoupon: (index), removes a coupon from the user's cart.

                    The following events are triggered:
                        - cart.updated  


// user
- user.createNewAddress: ( data, onFormDataErroneous, onError ), adds a user address.
                        - data: the form data
                        - onFormDataErroneous: a callback triggered when the form is invalid.
                                    onFormDataErroneous ( formModel )
                                            - formModel: The on-the-fly formModel representing the erroneous form 
                                    
                        - onError: a callback triggered if an error occurred server side.
                                    onError ( errMsg  )
                                            - errMsg: string, the error message that ekom suggests to display

                    The following events are triggered:
                        - user.address.updated  (only in case of success)
                        

// utils
- debounce: https://davidwalsh.name/javascript-debounce-function




 
Here are the ekomJsApi private methods:

- request( type, action, data, success )
    - type: gscp|html|json, ekom use almost exclusively gscp (/service/Ekom/gscp/api)
    - action: the action parameter to pass as $_GET\[action]
    - data: an array of data to pass via $_POST
    - success: callback executed on successful return

    




Events
==============
To subscribe to an event, we can use the **on** method of the ekomApi, like this:



```js
ekomApi.on("cart.updated", function(){
    // my code
});

```


Below is the list of available events with the arguments passed to them:

- cart.updated ( cartInfo )                         // fires when item is added, removed
- cart.itemAdded ( cartInfo, product_id, qty )      // fires when item is added



