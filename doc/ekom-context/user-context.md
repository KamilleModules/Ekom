UserContext
=================
2018-04-22



The UserContext is probably one of the most interesting part of the Ekom ecosystem.


Why the user context?
-----------------------
It is born out of the idea that the shop should display the products depending on the user watching them.

So for instance if the user is just a simple web visitor, the shop would display the default prices,
but if the user is connected and is a professional, then the shop would display prices with vat excluded.


Implementing the user context
-------------------------------

When it comes to the implementation, things get a little bit complex, and it's easy to forget how the UserContext
really work, so this section below will ensure that we can always remember all the nitty-gritty details of
the implementation.


### What's affected?

If you think about it, the only variable that matters in a product is the price (i.e. the name of the product doesn't
change depending on the user watching it, but the price does), so the only thing we need to update depending
on the user is basically the price.

But the price itself is composed of various components (see ekom-order-model-11.pdf):

- the original price, which is the price set by the user (ek_product_reference)
- the real price, which is the price affected by a condition, generally being the user group (ek_product_variation)
        This price in our company was set by a third-party software (erp); it basically allows us to divide
        our products catalog from one to many (one per user group).
- the base price, which is the price with discounts applied on it (ek_product_reference_has_discount)
- the sale price, which it the price with taxes applied to it (ek_tax_rule_condition)
- the time, as a discount might apply to a given product only until a specific date


In other words, the price depends on:

- the user group
- the discounts applied to it
- the taxes applied to it
- the time





### What's our implementation goal?

In the new version of Ekom, our implementation goal is two-fold:

- we want to have all price variables modelized in the database, so that we can encapsulate all prices variations
        in one sql query.
        The promise behind this is that then the application becomes naturally fast without caching.
        This was really the HUGEST step for me, and as a day-to-day developer I REALLY noticed the difference
        (do you spot the difference between waiting for a product list for 20+s and not waiting at all?)

- provide a cache identifier, tailored to the user.
        The incentive being to say that we can even do better that one sql request to display a product list:
        we can do zero request.
        But the condition for that, since every price depends on the user, is that the cache identifier encapsulates
        the user profile.



### Segments: knowing your cache identifiers

Now if it's easy to understand that we want a cache identifier suited for every user.
But you know that in a list you have other params like:

- the sort column
- the sort direction
- the current page number
- the number of items per page
- the various filters:
        - price included between this and that
        - only include products with discounts of 10%
        - only include products with attribute size=xxl and weight=4kg


So, we know by advance that our base cache identifier will already be derived in multiple forms.
This alone might discourage you (or not) from trying to cache all product lists.

But let's say we are not discouraged.
We will apply a progressive cache strategy where when a user calls a new page, it automatically gets cached
for the next call.

Awesome idea right?

Ok, but what if our cache identifier is different for each user?
This progressive cache strategy would only work on a per-user basis.

This would mean that if user A displays page p1, then the cache for page p1
will only work for user A, and now if user B displays page p1, it will create another cache specific to user B,
and so on....

It would better if user A could create cache for user B right?

Well, segments is all about that problem: how do we create cache identifiers that applies not only to one user,
but rather to entire groups of users.


This is actually a pretty simple idea:
if user A and user B belongs both to group G1, then we will use the group name G1 in our cache identifier,
and not what's specific to user A and/or user B.

So by eliminating what's specific to an user, we end up with a cache identifier that applies to multiple
groups of users.


###### But what about discounts applied to specific users?


After re-thinking about it, it turns out Ekom doesn't provide a discount per user as for now,
since we don't need that in my company.
What we do in our company is coupons per user (I was confounding discount with coupons...)


@not implemented-----------

That's right, our segmentation is now confronted to this reality:

in Ekom we shall be able to apply a discount only to a specific user if we want.
So for instance, user A gets 10% on all products of category XYZ.

If we weren't pragmatic, we would say that this possibility alone would defeat the idea of
a cache identifier segmentation.

But let's be pragmatic: it turns out applying discounts to specific user is a scarce operation,
if used at all.

So what we can do is create a segment which variations are:

- null, includes all users which don't have a discount applied for them
- $userId, includes only this user


The idea is that our shop will probably contains zero, one, or two such privileged users, and so the
number of segments will never be to high.
If your use case is different of course, you need to think about another solution, but in my company,
the discounts per user feature is more theoretical than functional, except for maybe one user from
time to time...


####### What about time?

Time is also a problem if not segmented, since it's infinite (ooooh my god, we will never be able to
create cache identifiers for time).

Well don't fear my friends, we just need to segment it depending on our needs.
In my company for instance, I believe one segment per day is reasonable, maybe two.

Your use case might need more, maybe four segments per day, or even one per hour, but remember that
the more segments you have the more cache identifiers you will have (and it increases exponentially...)


In Ekom implementation, the time segment has to be represented by a datetime.
So if you have one segment per day, you will probably set the time part to 0:

- 2018-04-22 00:00:00

If you have two segments, you could use this for instance:

- 2018-04-22 00:00:00
- 2018-04-22 12:00:00


And so on.
That's because the datetime will concretely be used in the sql query statement as to test whether or not
a discount applies.




So now I've exposed my mind, be wise and understand all this before you update the userContext.




















