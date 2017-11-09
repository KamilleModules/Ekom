Current Checkout data
=================
2017-11-09




A risk for unsynced data
----------------------------

The shipping costs are displayed at various locations in ekom: the cart, and the checkout tunnel.
The prices are even displayed everywhere.

What if the user was told that product X is 10€ on the checkout page, but then back on the product page, we give
him a price of 8€.

As the owners of the applications, we must ensure that those kind of data are always consistent throughout the
whole application.

However, there is a risk that this unsync problems occur.

That's whenever the user (or even the application) changes those data without notifying the rest of the application.



The ekom solution
---------------------

The solution ekom chose is to provide one hook: onCheckoutDataUpdate ( type, data ).
And ekom developers should always listen and react to it.

The type is the type of event triggered, while the data is an array containing the relevant payload.

The different types handled by ekom are the following (you can add your own):  

- shippingAddress: the shipping address of the current user has been changed 
                        This might happen when the user changes it from:
                                - his/her account
                                - the checkout tunnel
- billingAddress: same as shipping address
- carrier: the carrier was changed  (during the checkout tunnel)
- paymentMethod: the payment method was changed (during the checkout tunnel)



On top of that system, ekom adds its own listener, as to make the ekom module consistent with itself.
The ekom listener stores the values in the session.
And provides

