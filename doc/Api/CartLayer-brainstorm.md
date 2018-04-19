CartLayer brainstorm
======================
2018-04-19






Cart storage?
-----------------

First question is: how to store the cart?

Session or database?

Database is good for statistical purposes,
session is good because it's faster (doesn't need db queries).


So ekom uses the session storage.
A db layer is added on the behalf of the modules (see EkomCartTracker module) to collect statistical data.


Structure of the session cart?
--------------------------------

- items:
    - 0:
        - token
        - quantity
        - box: the CartItemBox model
- coupons: array of Coupon model


The big question (at least for me) was:
- does the session contain the whole cart items, or pointers to cart items?

As you can see from the structure above, ekom now uses the whole cart items (before that, ekom used the pointers system).

With the pointers system, the cartItemBox is replaced by the product id and the product details properties.

Some benefits of the pointer system:

- the cart is always up-to-date

The drawbacks of the pointer system:

- first of all, it breaks the intuitive analogy of a real life cart: in the real life, when you add a product to your cart,
    it doesn't change ever. So for instance if you put a product with 10% discount in your cart, you benefit the discount
    even if the shop owner decides to cancel this discount between the moment when you put the item in your cart and 
    the moment when you fulfill your order.
    
    Same goes here, and having the whole item in the cart allows for implementing a system where the user gets what 
    she sees.
    
- then the pointer system is slower than having the whole cart items in the session.
    That's because the cart item needs properties that requires some sql queries (not just one sql query, but many, maybe 5 or more per product).
    Imagine that a user has 4 products in their cart, and multiply by the number of queries per item and you start to see the problem.
    With session, we only do the query once when the item is added to the cart.
    
    
Now that we have established that we are going to use the all-in-session system, we still need to address the following problem:
in ekom products monetary attributes depend on the user context.
Imagine the user connects, then the cart items should be updated as well.

In other words, every time the user context changes (the user connects, or the user changes her address, ...),
we have to update the items in the cart.


    
    
    








