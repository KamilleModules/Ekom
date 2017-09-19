Product Page
===================
2017-09-18


The product page is one of the most important page in ekom.

Here are some random thoughts about my implementation.


I personally like the concept of **virtual quantity** in ekom, and so I wanted to implement it.



There are three main areas of the product page to take care of (from what I see)

- display the page (product box) when you land on it
- display the page when you configure the product (attributes and or details) dynamically
- update the page when the user interacts with the cart


We actually can bind the first two areas in one group, because in both cases all what we need is the box model.

For the third area, we need to acknowledge the data coming from the cart as well.
Later in this document we will see how those data affect the current product page.






Display the page statically or dynamically
================================

So when the user lands on the page, or when she configures the page, we simply want to display the page reflecting 
the **product instance**.

Let's handle the first two areas.

If it's static
-----------------
The user lands on the page.
The only thing we have to tell us what instance it is is the uri.
From the uri, we will guess:

- the product id, which allows us to know exactly which attribute combination was used
- the product details or the token, which allows us to know exactly which product details were used

The product id is straight forward, I'm not going to explain anything about it.

Now for the product details, they are passed as parameters of the uri. This mechanism is explained in the product
details document.

So, typically we have such an uri:

- /product/1988?day=thu

As you can guess, the day is thursday in this example.

The token is used as an alternate way of accessing product details for some special products which instance doesn't 
change when the product details are changed.

For instance, in the ekomEvents module, the user can buy a pass, which is a product, and she can choose which courses
she wants to subscribe to. There are plenty of courses (course 1, course 2, ...), and for each course she can 
select the number of places she wants to book (make a reservation for?).
So in this case, if we decided that every time she changes the number of places of a course it's a different 
product instance, then in the cart we would have an item per product instance, which potentially a lot of item.

For instance I order the product with 6 times course 1.
Then I try to add the same product with this time 4 times the course 1, this creates another item in the cart...


Another way to see this problem is to say that all variations of places number belong to the same product instance
characterized by a token. 
Along with the token, we save all the details of the product instance (6 times course 1, 4 times course 2, ...).

This feels more intuitive for the human user, as she can now see a tangible product with configurable number of places.


But anyway, all params comes from the uri for the static approach.


If it's dynamic
-----------------
Why re-invent the wheel.
If it works for static, why not just re-use the static system.

That's the approach here.
The call from ajax emulates the uri with the desired parameters.

This is possible only because we know that all params come from the uri, and so the ajax service will receive the 
params as well, exactly as if a static page was called.

It means that there is some kind of js hooks, placed on the "add to cart" button, such as when we click 
the "add to cart" button we collect the necessary params before sending them to the ajax service.
Not a big deal.



What about virtual quantity?
---------------------------

You know what, we are mvc.
This means that the product box model that we generate has already been through all the necessary logic to display 
the template, and the template is just a dumb view displaying variables (with the exception of the js behaviour, 
which in my mvc implementation is part of the template, and is not so dumb, but you get the idea...).


So, what's the virtual quantity?
From the virtual quantity's document:

- virtualQuantity = stockQuantity - cartQuantity

So, if our model provides the virtualQuantity, this also means it will be aware of the cartQuantity.
End of the discussion.




Update the page after cart interaction
=========================================

First thing first, what needs to be changed on the product page after a cart interaction?

- the virtual quantity


A lot of trouble for just one number, don't you think?
If you think it's worth it, please continue reading.

So the main idea is that when the cart updates, we have the cart model at our disposal.
From then on, we should use the cart model to update the virtual quantity.

Seems simple, should be simple.
Time for implementors to get their hands dirty!


 























