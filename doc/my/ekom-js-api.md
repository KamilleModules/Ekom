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


Ekom js api covers many areas of the Ekom module.

One of the fundamental concept of this api is that it's mostly based on the event listener principles: 
there are some events, and you can listen to them.

Widget authors can also create their own events and trigger them when they want,
so that other people can listen to them.

 


Here are the ekomJsApi public methods:



// events
- on ( eventName, cb ): adds the cb listener to event eventName,
            the args passed to cb are defined on a per-eventName basis.
            Use the trigger method to then trigger that event manually.
            See the events section to see all the available eventNames.
            
- trigger ( eventName, ?args...): triggers an event 


 
// cart
- cart.addItem: (product_id, qty), add an item to the cart
                    The following events are triggered:
                        - cart.itemAdded
                        - cart.updated
                        
- cart.removeItem: (product_id ), remove an item from the cart
                    The following events are triggered:
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
- user.createAddress: ( data, onSuccess, onFormDataErroneous, onError )

                        Insert or update an user address.
                        
                        If data.address_id is set, this is an update, 
                        otherwise it's an insert.
                        
                        - data: the form data, those are transferred to the UserLayer.createAddress method:
                                    
                                  - first_name
                                  - last_name
                                  - phone
                                  - address
                                  - city
                                  - postcode
                                  - supplement
                                  - country_id
                                  - is_preferred: bool=false, in case the address is of type shipping, whether
                                                   or not this address should be the preferred one.
                                                                                 
                                                                            
                                                                            
                        
                        
                        - onSuccess: callback executed if the address was inserted/updated.
                                The onSuccess callback has the following signature:
                                
                                        onSuccess ( data )
                                            With: 
                                                - data.addresses: The result of the 
                                                        userLayer->getUserAddresses( userId ) method, which is 
                                                        an array of addressModel.
                                                        Each addressModel has the following structure:
                                                        
                                                      addressModel
                                                      ==================
                                                      - address_id
                                                      - first_name
                                                      - last_name
                                                      - phone
                                                      - address
                                                      - city
                                                      - postcode
                                                      - supplement
                                                      - country
                                                      //
                                                      - fName, string: a full name, which format depends on some locale parameters
                                                      - fAddress, string: a full address, which format depends on some locale parameters
                                                      - is_preferred, bool: whether or not this is the favorite user address                        
                        
                        
                        - onFormDataErroneous: a callback triggered when the form is invalid.
                                    onFormDataErroneous ( formModel )
                                            - formModel: The on-the-fly formModel representing the erroneous form 
                                    
                        - onError: a callback triggered if an error occurred server side.
                                    onError ( errMsg  )
                                            - errMsg: string, the error message that ekom suggests to display

                    The following events are triggered:
                        - user.address.updated  (only in case of success)
                        
- deleteAddress: (addrId, onSuccess)
                        Deletes the address (only if it's owned by the session user).
                        The onSuccess callback has the same signature than the createAddress's onSuccess callback.
                        
                    The following events are triggered:
                        - user.address.updated                          
                        - user.address.deleted
                        
- getAddressInfo: (addrId, onSuccess)
                        fetch the address model corresponding to the given addrId.
                        Then executes the onSuccess callback, passing it the address model.
                        
                                onSuccess ( addressModel )
                                
                        The address model is already defined above (see the createAddress method).
                        No events are triggered.
                                              
                                              
// checkout                        
- setShippingAddressId: function (id, onSuccess, onError, options)
                This sets the shipping address for the checkout process.
                - id: the id of the address to mark as the shipping address
                - onSuccess ( arr:data )
                            data.orderModel contains the order model
                - onError ( string:errorMessage )
                - options: (all options are optional)      
                        - marker: set a marker in session, which you can use to make the various
                                steps of the checkout process look persistent.
                                For instance, if the user has selected the address and goes to step two,
                                when she refresh the page, you can use the markers to redirect her
                                directly to step two.
                        - saveAsDefault: false
                                if you want to make the item (in this case the address) the user's
                                preferred item, set this to true.
                                It doesn't work with all item types.
                                Actually now it only works for addresses.
                                If it works, then next time the user goes through the checkout process,
                                this item (address) will be automatically pre-selected.
                                
                The following events are triggered:
                    - checkout.address.selected                                                
                                
                                                                                                                            
- setPaymentMethod: function (id, paymentMethodOptions, onSuccess, onError, options)
                This sets the payment method for the checkout process.
                - id: the id of the payment method chosen by the user
                - paymentMethodOptions: an array which help determining the payment method details
                            chosen by the user.
                            Not all payment methods use options.
                            The "credit card wallet" payment method is the reason why
                            paymentMethodOptions exist in the first place.
                            With the "credit card wallet" payment method, the user can choose
                            between different credit cards that she has registered.
                            The paymentMethodOptions in this case is used to identify which card
                            was chosen by the user.
                - onSuccess: same as setShippingAddressId                            
                - onError: same as setShippingAddressId                            
                - options: same as setShippingAddressId                            
                        
- updateProductQuantity: function (product_id, newQty)                                
                Update the quantity of a given product in the cart and for the checkout process.

                The following events are triggered:
                    - checkout.cart.updated
                    
- placeOrder: function (onSuccess)                                
                place the user order (creates the order in the database).
                - onSuccess ( data )
                        data.orderModel contains the order model

                        

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



