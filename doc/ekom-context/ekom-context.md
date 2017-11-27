Ekom context
================
2017-11-27



In ekom, there are some variables that are used for almost any situation.
Those are called ekom context and are the following:

- shop_id
- lang_id
- currency_id



Related
------------
See ekom product box context




Why ekom context?
--------------------


The main idea is to ease testing.

I realized (perhaps too late) that a lot of methods in ekom rely on methods such as E::getShopId to access
context level data.

While this is handy, I now believe that the developer should think twice before committing those statements to the code.
I now believe Api methods should be low level methods, and therefore require the variables they need to use, as to 
avoid confusion/unsync problems.



In the claws MVC system that ekom uses, the context data should ideally come ONLY from the controller (the top) and 
spread down as the php process is executed.
I believe this is the only way ekom can become a consistent/solid app.



