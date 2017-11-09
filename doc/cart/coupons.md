Coupons
==============
2017-11-09



Coupons are almost like discounts, but to apply them the user must provide a code.

Also coupons apply only on two targets (from ekom order model):

- linesTotal (default)
- cartTotalWithShipping




Combining coupons
=======================

By default, the user can only have one coupon in his/her cart at the time.

So, if the user tried to add another coupon, the newest coupon, if valid, would replace the oldest.


However, it might be the case that your app wants to implement a different behaviour.

When this happens, ekom provides modules with a hook.
See CouponLayer source code for more info.


