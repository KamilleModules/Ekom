CheckoutUtil: alternative checkouts
====================================
2017-12-10




Implementing a checkout process from scratch is quite time consuming.
If you need to implement an alternate checkout process that works almost like ekom's checkout process,
but with slightly different variations, then you probably are looking for a way to REUSE the
ekom checkout. 


Well, luckily for you, ekom checkout process was designed to be hooked like that.

The main objects that characterize the ekom checkout process are:

- the CartLayer
- the CheckoutProcess (EkomCheckoutProcess)


Ekom's implementation of the checkout process is such as if you could change those two objects
with your owns, you would have done 95% of the job.


Now to replace your objects with your own, you can use the CheckoutUtil object, which has the
following methods:


- getCurrentCartLayer
- getCurrentCheckoutProcess
- getCurrentCheckoutProcessModel (which is almost the same as getCurrentCheckoutProcess)
- getCurrentCheckoutOrderUtil, the object holding the placeOrder method
- getCheckoutThankYouRoute


The way it works is that each method give modules the opportunity to set a replacement object
for either the CartLayer or the CheckoutProcess.
At the end of the module hooks loop, if the objects haven't been overridden, ekom returns
its default objects.




Implementation guidelines
================================
Here is the guidelines I recommend:

Each different checkout page is called via a different uri.

For instance, the default ekom checkout page is called using /checkout,
while an alternate ekom estimate checkout page will be accessed via /checkout-estimate.


This allows us to target two (or more) different controllers.
At the beginning of a controller, the controller sets a session variable indicating
that he wants to take the hand on the other checkouts.

And so when the CheckoutUtil calls the module hooks, each module can detect whether or not
the session variable was set for him.


The session variable, I recommend that it's set using the CurrentCheckoutData object,
and the variable name:

- checkoutType: ekom (the default value set by ekom)





