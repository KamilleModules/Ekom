Coupons
===============
2018-04-27





When the user is on the "cart" page, she can add coupons to her cart.


When she does so, the coupon is not immediately added to her cart,
but rather the system checks whether or not she has the right to do so.

There are two types of checking performed by the system:

- conditions (for instance, is the date valid, is this coupon only for one user, or one user group,
        or only applies if there are two products of category 022 in the cart, etc...).
        Those are checks against the properties of ek_coupon starting with the "cond_" prefix.

- quantity checking, check against the quantity and quantity_per_user properties of the ek_coupon table.



The only problem with this system is related to the quantity_per_user checking:

- How can we check if the quantity_per_user is right when the user is not connected?




Quantity per user checking: the delayed technique
----------------------

Probably the most natural solution to this problem is the following:


When the non-connected user adds a coupon in her cart, we accept.
However, whenever she connects, we perform the quantity_per_user test,
and if it fails we discard the coupons, reporting the incident to the user.


### Implementation theory


- the non-connected user adds the coupon in her cart
- the system detects that the user is not connected, and so it cannot check the quantity_per_user yet.
        The legitimate user wants to see the effect of the coupons now though,
        so the system applies the coupon now.
        However, in parallel, it also puts the coupon id in session, for later treatment.
        (we want to be sure that when she connects, she pass the quantity_per_user test)


- at some point, the user connects (you must be connected to complete your purchase).
        At this precise moment, the system checks whether or not it has coupon ids to treat.
        If so, then it checks the quantity_per_user now.
        If the test fails, then the system discards the culprit coupon(s) and
        pushes a visual notification to the user so that she knows that the coupon(s) were discarded.





