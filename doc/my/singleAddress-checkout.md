SingleAddress checkout
==========================
2017-08-07



singleAddress checkout is a word to which many conventions are bound.

It basically represents the checkout process behaviour.


This is currently the only checkout mode implemented in ekom.



singleAddress
---------------
- the checkout process is composed of the following steps, in the given order:

        - the user chooses the billing address and shipping address (if shipping address is required)
        - the user chooses the carrier
        - the user chooses the payment method
        - the user pays
        - the user validates, we may have a payment confirmation feedback, depending on the payment method used


- the carrier step can be skipped if:

    - the merchant says so and sets the carrier to use
    - or if there is only one available carrier and the merchant allow automatic carrier


See (shop config).carrierSelectionMode property for more details.





steps
---------

Therefore, in singleAddress mode, the current step is predictable, and
thus can be set from the Model (mvc) level.


 
Special cases
----------------

What can happen is that the user hasn't created any address yet, 
but is on the checkout page.

When this happens, the billing/shipping address step should be the current step,
and the developer shouldn't have access to the address.

A variable like "userHasAddress" is provided by the model, so that the template
can display an appropriate response.





