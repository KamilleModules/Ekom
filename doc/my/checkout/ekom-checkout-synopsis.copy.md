Ekom Checkout synopsis
======================
2017-06-07



[![ekom-checkout-cheatsheet.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-checkout-cheatsheet.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-checkout-cheatsheet.jpg)



In ekom, we have different levels of information that allow a customer to purchase products on the website.

The levels are the following:


- cart
- shippings
- order sections




Cart level
===============

The cart accompany the user while she is browsing the products, 
it represents the intent of the user to buy some products.
 
It contains the following information:

- product id
- product quantity



Shippings
=============

In ekom, the term shippings represent something very specific: it represents the user's vision of how she distributes
the cart items into shipping addresses (for downloadable products, this section doesn't apply).


Usually, users do only one shipping, meaning they send all products to one shipping address.

But imagine Sally has 4 products A and 2 products B in her cart.
Now she wants 1 product A to be shipped at address "6 wall street", and 3 products A and the 2 products B to be shipped
at her other address "800 bd churchill".

She will then do two "shippings", the first shipping will look like this:

- 1 x product A     --> 6 wall street
- 3 x product A     --> 800 bd churchill
- 2 x product B     --> 800 bd churchill



Order sections
================


Now the user has chosen her shippings preferences.
However, it might be the case that the carrier available to a shop will not be able to ship the products exactly
as the user wants.

If that's the case, we need to inform the user.

So we create "order sections" to check for what's possible to do.
An order section is basically the combination of a carrier and an address.

Usually, a carrier will be able to handle all of the user's shipping(s).

So for instance if we have two carriers: ups and postit,
if ups can handle every products, then we end up with the following order sections:

- ups - 6 wall street (order section 1)
    - estimated shipping date: 6 april 2017
    - shipping cost: 10€
    - products
        - 1 x product A
    
- ups - 800 bd churchill (order section 2)
    - estimated shipping date: 10 april 2017
    - shipping cost: 11€
    - products
        - 3 x product A    
        - 2 x product B    

    
Potentially, we might have to distribute the user's shippings accross multiple order sections.
That happens if for instance ups cannot handle the wall street address.
In that example, let's say postit can handle the wall street address.
If that's the case, our order sections would look like this:

- postit - 6 wall street (order section 1)
    - estimated shipping date: 6 april 2017
    - shipping cost: 10€
    - products
        - 1 x product A
    
- ups - 800 bd churchill (order section 2)
    - estimated shipping date: 10 april 2017
    - shipping cost: 11€
    - products
        - 3 x product A    
        - 2 x product B

If postit cannot handle the wall street line either, then we need to inform the user, so that she can remove the item
from her cart. 
Hopefully that last case doesn't happen too often, and it shouldn't since the shop owner usually choose carrier
that can handle the shipping of all products, but as developers we have to take this case into account.






Implementation details
==============================
2017-06-09 --> 2017-06-13


We use three steps:

- step 1: create shippings
- step 2: create order
- step 3: choose payment method


To collect info, we start with a base array, and we add information at every step.
We call this array the order array.
Here is how the final order array looks like:


```txt
- order
----- summary: 
--------- grand_total: null|string, the formatted grand total
--------- shipping_costs: null|string, the formatted combined shipping costs
--------- ...maybe other things like tax details
----- payment_method:
--------- id: id of the payment method
--------- type: type of the payment method
--------- ...other info, specific to the payment method
----- sections:
--------- 0:
------------- address:
----------------- address_id
----------------- first_name
----------------- last_name
----------------- phone
----------------- address
----------------- city
----------------- postcode
----------------- country
----------------- supplement
----------------- fName
----------------- fAddress
------------- carrier:
----------------- carrier_id
----------------- estimated_delivery_date:   null|yyyy-mm-dd HH:ii:ss
----------------- shipping_cost: string, formatted cost of the shipping af all accepted items for this section
----------------- rejected:
--------------------- 0: same as items.0
--------------------- ...
------------- items
----------------- 0:
--------------------- product
--------------------- qty
--------------------- weight
--------------------- ...coupons?
----------------- ...
--------- ...
```


At the end of step 1, the shippings have been decided and the array looks like this:

```txt
- order
----- summary: (not set yet)
----- payment_method: (not set yet)
----- sections:
--------- 0:
------------- address
----------------- address_id
----------------- first_name
----------------- last_name
----------------- phone
----------------- address
----------------- city
----------------- postcode
----------------- country
----------------- supplement
----------------- fName
----------------- fAddress
------------- carrier: null|array, depending on whether we guessed the carrier or not (see the "more implementation details" section)
------------- items
----------------- 0:
--------------------- product
--------------------- qty
--------------------- weight
--------------------- ...coupons?
----------------- ...
--------- ...
```


At the end of step 2, the order sections have been chosen and the array looks like this:

```txt
- order
----- summary: (not set yet)
----- payment_method: (not set yet)
----- sections:
--------- 0:
------------- address
----------------- address_id
----------------- first_name
----------------- last_name
----------------- phone
----------------- address
----------------- city
----------------- postcode
----------------- country
----------------- supplement
----------------- fName
----------------- fAddress
------------- carrier:
----------------- carrier_id
----------------- estimated_delivery_date
----------------- shipping_cost 
----------------- rejectedItems:
--------------------- 0: same as items.0
------------- items
----------------- 0:
--------------------- product
--------------------- qty
--------------------- weight
--------------------- ...coupons?
----------------- ...
--------- ...
```

Then, at the end of step 3, the user chooses the payment method and the array looks like the one at the beginning
of this discussion.

Note: steps could probably be arranged in any order.
Note2: this technique doesn't take into account multiple payment (i.e. the user only pays once).


More implementation details
============================
2017-06-09


Create shippings
-----------------

Client side, we have a button:

```html
<button class="button save-step-shipping">Ship to this address</button>
```

If you are in single address mode, you can just pass the address id to the api, and the api will create the shipping
for you, using the cart data.

If you are in multiple address mode, you will need to find other heuristics but the same mechanism will be used:
click the "save-step-shipping" button, so this means that you probably need to collect the shipping data
via a gui and post them when the user clicks on the "save-step-shipping" button.








Create order
---------------

Traditionally, from what I know e-commerce let the user choose between different carriers.
However, in big websites like amazon, there seems to be a tendency to make the choice automatically for the user.



I personally like the second method better (the fewer questions asked to the user the better),
but in ekom we provide a configuration key that would allow the developer to choose different options.

carrierSelectionMode:

- fixed:$carrier_name, the carrier is fixed (by the shop owner) to the value $carrier_name.
- auto: ekom will choose automatically, using the first carrier that can handle all of the products
- manual, the user will choose between the carrier available to the user (unless there is only one carrier
            choice in which case the choice might not be asked)
            

Note that because the carrier can be chosen automatically, what we ended up doing in ekom is that
            when the "save-step-shipping" is done, we also perform a check on whether or not the
            user will have to choose the carrier.
            If there is no choice, then we apply the "carrier layer" in the same row, thus saving one 
            round-trip (in a typical ajax driven checkout page).
            
            




Payment methods
-------------------

Here is my idea so far:

$h = ekomApi::inst()->paymentLayer()->getPaymentMethodHandler("creditCart")
$h = ekomApi::inst()->paymentLayer()->getPaymentMethodHandler("paypal")

PaymentMethodHandler:
- getMethodBlockModel ()

### What's a method block


The gui let you choose a payment method (paypal, ccard, ...).
So each payment method is represented by one or more block.
 
However, each block can contain more than one selectable item.
For instance paypal is just one item, but a credit card wallet payment method could have one item per credit card,
which might be more than one item.

So the paymentMethodHandler provides the methodBlock, which is a model containing an items entry.

The items entry represents the selectable items.

Apart from that, the theme needs a little bit more knowledge about the payment method,
because in some cases you need more than just displaying items.

For instance, the credit card wallet let you add credit cards, so this means there must be an "Add credit card"
button somewhere. That's the theme (or at least template) that knows how to display the selectable items and the button
or anything that goes with it.

In the case of the credit cards wallet, we also need the api that allows for adding new cards,
this is also part of the theme's knowledge.

(The paymentMethodHandler author must provides all the implementation necessary info though)

So, different templates have different abilities.

To make things easier for template authors, every selectable item should have at least one key:

- type: string, indicate the type of the paymentMethodHandler














