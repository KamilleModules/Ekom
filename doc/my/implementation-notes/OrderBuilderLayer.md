OrderBuilderLayer
======================
2017-08-07



This is my implementation brainstorm for the OrderBuilderLayer object, 
which tries to implement ideas described here: 
class-modules/Ekom/doc/thoughts/about-checkout-process.md.

The singleAddress checkout model is used.





Using the session
---------------------
The data of the current "in-construction" order is stored
in the session, so that we can easily keep track of the user choices (
which payment method, which address, ...
).

Since it's in the session, we tend to store only the strict necessary data (for instance
we don't store the cart items, since they are already in the cart), we use
the lazy approach.
If the user uses a coupon, we store just the id and we will recompute the numbers
on every page refresh (if required).

Since the user can change her cart, this also means coupons "granting" 
needs also to be recomputed every time.


The main benefit is the small footprint we have in the session.


We will store the following data in the session:

- billing_address_id: false|int,
                    if false, means that the user hasn't created any address yet.
                    Otherwise (if the user owns at least one address), the 
                    billing address id is automatically set here.
                    
- shipping_address_id: int|false if not required
- carrier_id: int|false if not required
- payment_method_id: 
- payment_method_options:



The items are the cart items, and they contain the coupons if any. 



Init and clean
---------------- 

- init
    If the session data doesn't exist yet, create it
    with user default values found in the database or elsewhere.
    
- clean
    Once the order has been placed (which means the payment has been initialized/sent
    and the only step left for the merchant is to capture the money),
    clean the session, so that a new order can be placed.
    
    
    
About payment initialization:

- for credit card: the user has entered her credit card information and confirmed
        the payment. The bank has accepted the transaction
        (number is valid and the money could be captured).
        
- for cheque, the user has said she will sent it to the merchant.          



The following methods allow template authors to know which step they
should display to the user.




getCurrentStep
-----------------

Since steps are well defined in singleAddress checkout mode,
this method returns the name of the current step.

Possible names are:

- address 
- carrier 
- payment
- ok, ready to place order


 
requiresShippingAddress
-----------------
Returns whether or not a shipping address is required.
In other words, whether or not the order contains a non downloadable product or not.

In ekom, downloadable product is a product with weight 0.
So, if the order's weight is 0, this function returns false, otherwise, it returns true.




 











