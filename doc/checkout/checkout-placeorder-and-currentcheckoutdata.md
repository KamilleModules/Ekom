Checkout placeOrder and CurrentCheckoutData
===============================================
2017-11-25


It illustrates how the CurrentCheckoutData is used during the checkout process.


At any moment, the user can go into her/his account and change her/his preferred billing address, shipping
address, payment method.

Then on the cart page, we see that the widget in red color needs the shipping address id,
and so we can use the simple algorithm to provide it:


```txt

shippingAddressId ?
try in CDD first, and if not set use the preferred user shipping address id, which is her/his default shipping
address id.
 
```


Lazy Checkout var resolution
================================    
    
Note: when the CDD initializes, we don't put all default data in here.
That's what we did before but it has a flaw.
If the user touches the checkout page, now if she goes to her account and change some preferences,
her preferences will only be seen on the next purchase (not the current one), which is annoying.

So instead we rather use the lazy approach of fetching the values only when we need it.

This allow the user to touch the checkout page, go back to her account, change preferences, and
the preferences apply immediately on the current checkout page.

However, once the user SELECTS the shipping address id via the checkout gui, that becomes the 
shipping address id until the purchase is completed (or until the user SELECTS another shipping address
via the checkout page).




placeOrder as a simple call
==============================

In our php api, we have this placeOrder method.
My idea is this:

```php

case checkout.placeOrder
    context = CDD::all
    out = []
    Checkout.placeOrder ( context, function () use ( &out ) {
        // on success
        // note: do things sync (not async, even though it looks like async)
    })

```