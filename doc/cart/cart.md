Cart
===========
2017-09-19 -- 2017-11-09


This document extends the cart-2017-09-19.md document and supersedes it in case of conflicts.




The cart in ekom is used to hold products chosen by the user until she purchases them.


Storage of the cart has already been discussed in the previous document.
Content of the cart contains two main entries:

- items
- coupons



Token
---------
To store an item in the cart, we use the token.
The token can be seen as the ticket to interact with the cart items (in a programmatic sense).


The token is defined by the following formulae:

- token: <productId> ( <-> <majorProductDetailsHash> )?
- productId: int, the product id
- majorProductDetailsHash: string, a hash of the major product details (see the product details document for more info)


So, each item's id property actually represents the token.



Product instance
------------------
A token identifies a **product instance** in the cart.

There are two types of product instances:

- configurable product instances
- not configurable product instances


Configurable product instances have minor product details, while "not configurable product instances" don't.
With configurable product instances, you can change the minor details; it doesn't change the product instance. 

Product details are explained in the product-details.md document.



  



Actions of the cart
---------------------
There are three main actions that we can perform on the cart:

- addItem: add/replace an item in the cart
- updateItem: update an item quantity
- removeItem: remove an item from the cart


An item in the cart is actually a product instance.



### addItem

This action is typically associated with the "add to cart" button of the product box page.
Every time you push this button, the addItem function will be called.

To add an item, you need the productId, the quantity, and the product details if any.
The ekomCart will take care of generating the token.

The generated token is generated once for all; from then on, and from then on only,
you can use the token to trigger the other cart actions.

EkomCart needs to differentiate between minor/major product details, therefore the productDetails
parameter is actually an array containing the following entries:

- minor: array of key/value pairs containing the minor product details
- major: array of key/value pairs containing the major product details

Note: the major product details will be used to compute the token.

If you add an item, then configure minor product details, then add the item again, the token 
won't change, but the minor product details will and will replace the old ones.
        
       


### updateItem

This action is typically associated to plus/minus boxes around the cart quantity.
Once an item is in your cart, it allows you to change the quantity of product instances in your cart. 

This takes a token and a quantity as input, and does the job of updating the quantity for a particular cart item.


### removeItem

Just what you would expect; takes the token as input.






Structure
----------------
```txt
- cart
    - $shopId
        - items
            - 0: 
                - token: token
                - id: the product id
                - quantity
                - ?details: the product details array, see product-details.md for more info
                - ?bundle: the bundle id if this product was added as part of a bundle
                - ?...extra properties 
            - ... 
        - coupons: array of couponId
    - ...
```



The cart and the ekom product box context
-----------------------------------------

When you think of the relationship between the cart and the ekom product box context, you might be surprised.
Notice that the cart doesn't store the **ekom product box context**.

That's because the ekom product box context is highly dynamical by nature and is affected by different things (for 
instance if the user is connected, or if the lang switches from eng to fra, or if the currency changes, or the shop 
changes,...).

This means that the same item in the cart, could have a different look and price (different language to describe it,
different price rules applied to it, different currency used, or even the product doesn't exist anymore if we
jump to a shop that doesn't use the product, but that's rather an edge case) depending on the epbc.

There is nothing wrong with that, it's just that the epbc is the context in which a product expresses itself,
and that includes a product in the cart.










