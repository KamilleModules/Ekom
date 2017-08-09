Cart checkout and order
===========
2017-06-02



In ekom (I don't know if other systems use the same approach),
and order is considered unmutable (it's like a written proof of a financial transaction).

The order table in ekom is one of the tool which implement this logic.

In order to be consistent with this, an entry in the order tab only when the user has validated the payment.


That's in an ideal world with only instant payment methods.

However in the real world, some payment methods like checks delay the actual payment to a later time.

That's why we have the order status table.

But still, in ekom we consider such a delayed payment as a concrete payment (the signed intention of payment,
which can resolve in either a concrete payment or a justice fraud, which is obviously out the scope of our module). 


So, concretely in ekom we have 3 relevant pages (exact names might change in the future):

- the cart summary
- the checkout page
- the payment accepted page


The main point of this discussion is that the order is only created when the "payment accepted page" is displayed.
This means that on other pages, the session cart is used, and cart discount/ product discounts are applied dynamically
until the last step (the user payment).






