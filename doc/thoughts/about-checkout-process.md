About checkout process
==========================
2017-06-14



This document is a reminder of how the checkout process works.



The user goes to from the cart page to the checkout page (or she also can go to the checkout page
from a product page).


If the ekom.order.$type session variable is not present, it's initialized.

It contains slim info about the different order steps.

Something like this:

- billing_address_id
- shipping_address_id
- carrier_id
- payment_method_id


Those are the user default preferences at first, and become the user choices then (with the help 
of the gui). 



The different order steps might be:

- choose the address
- choose carrier
- choose payment method


Those steps may vary depending on the template or the theme.
Some steps might be added, some other removed, the order modified, etc...


Then, the model info are retrieved from that slim model.
Those model info can include things like the payment method preferences for the connected user for instance,
or the details of the address, etc...


As we've said just above, the session contains the preferences or user choices.

In some cases, ekom won't be able to provide a default value, and so the value of null will be used.
For instance, if the user doesn't have a billing address yet, then the billing_address_id session
value will be set to null.
In those cases where the value is null, it's easy for the template to deduce that this step needs
to be completed.

However, when ekom provides default values (and it sure tries to provide a maximum of default values
as to help the user with the task of fulfilling the form), the template cannot possibly know
whether the number is a default value, or the value overridden by the user.

Therefore, the template must keep track of the checkout steps for itself.



Some helpers might be provided by ekom.
(no info on that now)


The ajax api
===============

Considering the above information, here is the ajax api provided by ekom.
It basically allow the template author to fulfill the session variables she wants:

- setBillingAddressId
- setShippingAddressId
- setPaymentMethodId
- setCarrierId
 
 
Now since we already know that a template author will want to keep track of the current step, but yet we don't
know exactly how she will organize those steps, we add a mechanism, layer on top of the ajax api, that allows
the template author to mark check points.

A check point is stored in the session as the current_step key. 
 
- billing_address_id
- shipping_address_id
- carrier_id
- payment_method_id
- current_step: 0

We start with a current step of 0, and let the user set any value she likes.
When the page is refreshed, the relevant OrderController also provides the current_step variable
to the template, so that template authors can implement a checkout system with persistent states.

So, here is the implementation of the ekomJsApi 

- setBillingAddressId ( billingAddressId, onSuccess=null, onError=null, options=null )
- setShippingAddressId ( shippingAddressId, onSuccess=null, onError=null, options=null )
- setPaymentMethodId ( paymentMethodId, onSuccess=null, onError=null, options=null )
- setCarrierId ( carrierId, onSuccess=null, onError=null, options=null )


So, the same pattern is used for the arguments of all methods:
- the id
- the onSuccess callback, onSuccess ( data ), data.orderModel contains the orderModel
- the onError callback, onError ( errMsg ), errMsg: an error message
- options: if not set or null, is not used. Otherwise, it's a configuration array with the following keys:
    - marker, if not set or null, is ignored, otherwise sets the current step (proxies to the setCurrentStep method) 
    - saveAsDefault: bool=false, if true, will save the current id as the user's preferred choice




