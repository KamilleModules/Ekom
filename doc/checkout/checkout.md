Checkout
=================
2017-11-09



The checkout in ekom is handled by the CheckoutPageUtil object.

Don't mind other objects that might be left in the code base, those are old tries that didn't make it.




CheckoutPageUtil provides interesting hooks:

- Ekom_CheckoutPageUtil_onCheckoutNewSession: triggered only if the checkout process is not started (see current-checkout-data.md for more details)
                            and when the user enters the checkout process for the first time (until the checkout 
                            data is cleaned again).
                            Ekom module uses this hook to fill the current checkout data
- Ekom_CheckoutPageUtil_onStepCompleted: triggered every time a step is completed





