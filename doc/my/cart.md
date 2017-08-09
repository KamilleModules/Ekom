Cart
===========
2017-05-27


So the first problem with the cart is: how do we store the cart, right?

Are we using a database, session, a self made up file system, ...?


Before handling this problem we have to answer a few questions.

Before I start, let's agree on how the cart system should work in general:


- if the user is connected, she's got access to her cart. Then she disconnects. 
        If the user makes the purchase to the end, then the cart is emptied after the order has been placed.
        If she leaves her cart without placing the order and disconnects,
        then later when she reconnects, she finds the cart in the state it was (it has been saved somehow by the ekom module). 
    
- if the user is not connected (i.e. we don't know who that is, he/she is anonymous), 
        then we can still keep track of the cart, so that we can later investigate the "why did this user not purchase?" question,
        and hopefully improve our system to raise the conversion rate.
        
        However, this is more an extra-feature that comes in a second implementation phase,
        and is not as primordial as the connected user's cart (which is indispensable).
        So, this handling of anonymous user's cart might be delegated to an ekom plugin, but we will still 
        think about it in this document.
        
        Now if the anonymous user becomes a logged in user, then the connected user synopsis applies, and the cart
        follows him/her as the anonymous user is being "promoted" to a logged in user.



Now that the general mechanism of our cart has been established, let's dive into the questions.





The cart content
-----------

- what's in the cart?

I see two main alternatives:

- storing the shop_id, quantity and product_id info, so that the cart is always up to date (synced) with the application
- storing every details of the cart (quantity, label, prices, taxes, discounts, ...) for faster rendering, but at the risk of being out of sync with the app


As I suggested, there is a risk of being de-synchronize from the app, what is that risk and how should we handle it?
I don't know yet, follow me.

So for instance, you know that the app stores almost everything in the database (the product attributes, the discount rules, etc...).
 
So let's take a few unsync examples:

- user A adds a t-shirt in her cart with a 10% discount, then disconnects.
    Meanwhile, the shop owner decides that this discount is no longer valid and remove that discount.
    Then user A re-connects.
    What happens?
    
- user B adds a t-shirt in size 6 in her cart, then disconnects.
    Later when she reconnects, it turns out that all t-shirt size 6 have been sold (quantity=0).
    What happens?
    
- user C adds a t-shirt in size 6 in her cart, then disconnects.
    Later when she reconnects, it turns out that the shop owner has removed the size 6 product attribute.
    What happens?
    

So, if the user A, B or C has every information in the cart, and if we rely only on those information to display
the cart, then it's easy to see the risk of a cart that could be out of date.

Is that a problem?
What could happen if a user had an out-of-date cart?
She could go to the place order screen with a price being 10€, and actually pay 100€ in the case of a removed discount.

There are two different approaches to this problem, depending on whether you want to protect the customer or the shop owner.
 
You might say: 
- well, it's not the user fault, she (the user) put an item in her cart which price was 10€, she should be able to afford it at 10€

Or you might say:
- well, the user should have purchased the item at the moment she put it in her cart.
        Now that she comes back, and the rules have changed, she has to play by the new rules.
 
And depending on your answer, you might allow or not allow out-of-date cart.
 
But don't think to much about it, because I've chosen already.
There is no such thing as an out-of-date cart, that's too much problems that I see coming as a dev.


Update: see my cart philosophy section later in this document 


And if you take the real world analogy it makes sense: imagine you go to a big shopping place and you put
items in your card. Now someone calls you: you have an emergency, you must leave the place.

Now when you come back any time later, first you will be lucky is your cart has not been emptied right?
But let's just pretend that you left only for 1 hour and when you come back, you find your shopping cart in the exact
same place where you left it, and all items are still in it, lucky you.

However, if the price has change, or if a discount has expired, there is no way you can argue that you should pay 
the price at the moment where you put the items in your cart, that's just not how it works. Only the payment line
counts.

However, for the edge case of an item that has been removed from your cart (because the shop owner doesn't sell that product anymore),
arguably the product cannot disappear by itself from your cart, BUT, in this case we will say that when you went off,
someone (suspectedly somebody in the shop owner's team) did remove that product from your cart.

So, this real world metaphor works (at least for me, hopefully you agree) and help us understand that out-of-date cart is not an option.



So, that answers the first question:

- What's in the cart:
    - quantities, and product_id(s).
            
            We will be seeking the other cart info from those ids.
            
            - what if id is deleted from the database? (you might say)
            
            then the cart naturally doesn't contain this product anymore.
            In other words, the cart is always synced with the app.
            
            Likewise, if a user put a product without discount in her cart, and disconnects,
            and meanwhile the shop owner creates a discount for that product.
            Then when the user reconnects, she benefits the discount for that product.
            
            That makes things simple to understand for everybody (and simplicity is always what I'm seeking).

    - coupon codes.
            A non connected user shall be able to apply coupon codes to the cart, see the changes that it made,
            before she is asked to connect.
            In order to do that, we need to somehow store the coupon codes entered by the user.                                            



Storage
-----------
Now what about the storage, how do we store the cart?



What are our options?

- database, slow but steady
- sessions, fast but you can loose them if the user change devices, or if she deletes her cookie
- a self made system


To be honest, I don't like database, it feels slow.
I don't like sessions either, because if you use session_regenerate_id, which is recommended security wise,
        it recreates the session files, and even worse, you cannot keep track of the user if she changes devices,
        unless you use her id as the session id;
        but ekom is just one module, what if other modules want to set the session id as well, we would have conflict.
        

So, there is only one option left, my solution (as always):
        
What we want is create an **app/data/Ekom/carts** directory.
        
Then we want to give each user a file. Since an user is uniquely identified by her id, we will use her id,
but the hashed version (h/a/s/h/e/d), since we don't want to  end up with a directory containing 1000+ entries.

So for instance, for user #1566, we will store/get her cart info in the following file:

- app/data/Ekom/carts/1/5/6/6-$shopId.php

Where $shopId is replaced by the current shop_id (for instance 1, or 4, ...)

Remember, accessing the filesystem is always faster than accessing the database (afaik), so now we are starting
to be fast :)

Then, the file might contain a serialized array with the following structure (or similar):

```txt
- items:
----- 0:
--------- quantity: 5
--------- id: 650
----- 1:
--------- quantity: 1
--------- id: 12
----- ...
- coupons: array of valid coupon ids
```

So now we can easily/quickly access and store info of a connected user's cart.
Of course, we will only take the items of the cart that are relevant to the shop being browsed (in the above example, shop_id=2).

But wait, traditionally people use the database for storing cart info, so what's the catch? Is there a hidden drawback?

As I see it, you store data in a database if you intend to search them.

In the case of a cart, we just want to persist a connected user's cart (for now), so I don't see any hidden drawbacks.

However, at some point the shop owner will want to investigate the cart data, and she will require some search ability.

Well, my recommendation is that when the shop owner wants to do some investigation about the data, then
a temporary (or persistent) cart table is created (by a not-yet-existing ekom utility), so that it eases the searching
of data. Or, alternately, ekom could create a file based search system, everything is possible, but my point is
that we give priority to fast access/storing of cart data so that the user (and our server btw) doesn't 
suffer the performance penalty of the shop owner's will to investigate the cart data.


So, custom system it is.

But wait, my system isn't complete yet.
What if I want to know WHEN the user did remove that item from a cart?, or WHEN she add another 3 items?

Hummm, I would add a second file for those cart-updates.

Keeping the same philosophy, but basically just storing another layer of data.

So, the file would be:

- app/data/Ekom/carts/1/5/6/6-update.php

And the content would look like this:



```txt
- changes:
----- 0:
--------- time: 2017-05-27T22:38:46Z
--------- shop_id: 2
--------- quantity: 5
--------- id: 650
----- 1:
--------- time: 2017-05-27T22:39:04Z
--------- shop_id: 2
--------- quantity: 6
--------- id: 650
----- ...
```



As you can guess, this file might be more verbose, and when the file gets too big, it might have an impact on performances.
What we need here is just to store data (we will postpone reading data to the investigate phase).


Afaik (https://stackoverflow.com/questions/23882138/php-file-put-contents-performance-on-750-thousand-lines-text-files)
appending lines to a file, even a huge one does not have significant impact on performances,
so let's update the content, and instead of having a serialized array, we will just put a stack of lines,
which might look like this (plus, we could do a rotate based on file size, in parallel, actually that might be necessary).


For the filenames:
- app/data/Ekom/carts/1/5/6/6-update-2017-05-25--14-52-05.php
- app/data/Ekom/carts/1/5/6/6-update-2017-07-01--12-08-59.php
- app/data/Ekom/carts/1/5/6/6-update-2017-10-30--08-47-42.php
- ...

For the content:

```txt

--- time: 2017-05-27T22:38:46Z
- shop_id: 2
- quantity: 5
- id: 650
--- time: 2017-05-27T22:39:04Z
- shop_id: 2
- quantity: 6
- id: 650
--- ...
```

So now our cart read/store system with statistical info is complete.
        
                
Anonymous User's cart
--------------------------                
Ok, but that was just the connected user's cart storage, now what about the anonymous user?
We shall provide her with a cart too because, she can potentially become an user.
                
I believe php sessions are the quickest/easiest way to get the job done.
                
Couple of other options:
                
- cookies: why not?
                    but the cookie is sent on every http request, so we prefer
                    to have it at a minimum size (just containing the session id is ok)
                
- localStorage, or any other webStorage:
                    this is designed for client side access,
                    but as a php dev, I intend to have access to the cart content server side,
                    because basically the cart is displayed by a template, which means I should
                    provide the model for that cart, 
                    and I just don't feel like making an ajax request every time just to get
                    the f... info out of the cart.
                
So, php session it is.     
   
   
What about the session data structure?

I suggest: same as the customSystem, but changing the key of items to ekom cart (since
sessions can be shared with multiple modules):

   
```txt
- ekom
----- cart:
--------- $shopId:
------------- items:
----------------- 0:
--------------------- quantity: 5
--------------------- id: 650
----------------- 1:
--------------------- quantity: 1
--------------------- id: 12
----------------- ...
------------- coupons: array of valid coupon ids
```   
   

A valid coupon id means that the application has verified that the coupon exist and was accepted according
to ekom coupon mixing/merging rules (see more details in the latest $date-database.md document).
   
   



Grand synopsis recap
========================

So now that we've chosen our weapons, let's go through the process again.


The customer visits your website, she is not connected, and add some items in her cart.
Ekom keeps the cart info (shop_id, qty, product_id) in the session.
 
For the sake of this discussion, let's pretend the cart is in $_SESSION\[cart].

Then she is satisfied with her cart and want to purchase the items, so she goes to the payment/order page.

Now she must login, so she logs in.

At that moment, the session cart is copied to the customSystem's cart (remember the customSystem?).

But wait, now she change her mind and adds two more items in her cart.

Since she is logged in, the items are added to the customSystem's cart.


Now the important question: are the cart info ALSO stored in the session?

Let's keep this question in mind for now and finish the process.

So, now with those 2 items added the customer is happy and goes back to the purchase page, and complete her order.

She pays, and the order is saved in the database, her customSystem's cart is emptied, and the session cart is 
emptied too.


Now back to the question:
if a user is connected, (the customSystem's cart is used, but) should the session cart be updated as the user updates the cart?

- if so, then when she adds 2 items to the cart, she could disconnect, make a pause, and then when whe comes back later,
    she could browse the website as an anonymous user WITH her anonymous session cart up-to-date.
    The downside is the redundancy of carts (one in the custom system, plus one in the session)
    
- if not, then she adds 2 items to her cart (as a logged user), and the session cart doesn't know about those two items,
        so that if for some reason she disconnects (voluntarily or by natural timeout), then if she browse the website
        again, the 2 items will be missing from her cart.

        
Unfortunately that's not something I would like to be the victim of as a potential customer of a website,
so I guess we don't really have the option here: the session cart must be up-to-date at any time.


You might wonder: why not use only sessions then, and drop the customSystem?
As said earlier, we could, but the customSystem is persistent, whereas the sessions MIGHT not be, for instance if the 
user deletes her cookies, or if she browse your website using another device.






Implementation
====================

Or, notes to myself.

encapsulating in ekom methods to abstract the hybrid storage problem:

- EkomApi::inst()->cartLayer()->addItem(productId, qty=1)
- EkomApi::inst()->cartLayer()->updateItem(productId, qty=1)
- EkomApi::inst()->cartLayer()->deleteItem(productId)  // alias for updateItem(productId, 0)
- EkomApi::inst()->cartLayer()->getItems()
            - shop_id: shop_id
            - items:
                    - \[product_id, qty]
                    - \[product_id, qty]
                    - ...
- EkomApi::inst()->cartLayer()->getMiniCartModel() // use getItems info to return a computed miniCart model 
- EkomApi::inst()->cartLayer()->getCartModel() // use getItems info to return a computed cart model
        

Also, we will have methods in the js ekomApi, see the ekom js api documentation for more info.










What if the user shops as anonymous, then reconnects?
===============================================

What if the user had a cart stored in the system, then disconnects and forget about it.

Then, a few days later, she browses the website again as anonymous, and add items to her cart.

Then she reconnects.

What should be in the cart?

- only the products that she has just put into her cart?
- the products that she has just put into her cart, plus the old products that she had before?


To me, the first solution seems more simple/appealing.
In terms of metaphor, the user went to the shop and filled a cart, then went out of the store.
One of our employee kept the cart for her in case she returned, but then as she returned, she took
ANOTHER cart and starts adding new items in it.

She knew that our employee was here for her (or she forgot), but she decided to go with a new cart,
and so our employee, being polite, doesn't disturb her during her shopping session, and silently 
gives the old cart to our recycling team.

This process feels smooth for the user, and is simple to understand for the developer, isn't it?











cart philosophy
==================
2017-06-06


Quote:
    But don't think to much about it, because I've chosen already.
    There is no such thing as an out-of-date cart, that's too much problems that I see coming as a dev.
    
Actually, I thought about it again for a coupon discount, and my conclusion was different, below is what I thought.


The shop owner creates a coupon abc which gives 5€ discount to any product.

Use case A (payment within 5 minutes):
2017-06-06: 10:00:00, the user adds that coupon to her cart.
2017-06-06: 10:02:00, the shop owner removes the coupon for her shop.
2017-06-06: 10:05:00, the user goes to the checkout page and pay.


Use case B (payment 7 days later):
2017-06-06: 10:00:00, the user adds that coupon to her cart.
2017-06-06: 10:02:00, the shop owner removes the coupon for her shop.
2017-06-13: 10:00:00, the user goes to the checkout page and pay.


In case A, it might seem logical (at least from the customer's perspective) that the user will benefit the coupon's discount.
In case B, it could be a mitigated battle.


Ideally, we need to think in more details about how we want to handle those cases.
 
My first approach (the cart is never out-of-sync) is a simple approach, but it doesn't feel very human,
and customer might complain.

That being said, shop owners won't update coupons every tuesday (or will they?) if you know what I mean, 
so depending on the frequency a shop owner updates her coupons, we might adapt our philosophy.


Nonetheless, my deadline arriving to its end, and given the fact that I've already implemented the cart-never-out-of-sync system,
I will continue with system for now, but keeping in mind that this area might evolve.

Actually, I could think about that and found out that we might get away with a simple workaround:

- basically giving a one day protect "badge" to a user (putting the badge in the session).
    What that protect badge does is that if the user pays her item the same day she put her coupons in her cart,
    she will pay the price affected by that coupon (the coupon state being the state it was when she added it into her
    cart, and not necessarily the current state of the coupon).
    
    Basically, that means that in case A, the user benefits the discount.
    Now to implement this, we basically need to store the state of the discount in the session (not just the id of the discount).
    As I said, I let that for a later moment, when I'll get that problem back in my face;
    because there is a slight chance (although I doubt it seriously) that we won't need that fix.
    









Sources:
- https://brockallen.com/2012/04/07/think-twice-about-using-session-state/