Product Page implementation
===================
2017-09-18



So let's get those hands dirty!
(sounds like a slogan)




Handling the Static and dynamic part
======================================




Attributes
--------------

When the customer buys a product, she has to know exactly what product she wants before adding it to her cart.
If the product has attributes, she needs to define exactly which attributes combination she wants.

In ekom, each combination of attributes leads to a unique product id.


And so my first step into the attributes system implementation was the following idea:

- for each attribute combination, the model provides a link to an ajax service displaying the corresponding product
    
This idea is based on the two following ideas:

- the computing is done in the model, not the view      
- I personally prefer the technique of refreshing only the parts of the page that change rather than the 
        whole page if possible (although it has minor negative impact on seo, it comes with a minor performance optimization)      



The uri passed to each attribute looks like this:


- /service/Ekom/json/api?action=getProductInfo&id=332


This is the base idea of the implementation.
Notice that the id is passed via $_GET, and represents the relevant product (i.e. the product matching
the selected combination of attributes).



Attributes might come in different forms, so far I've encountered the following:

- clickable links 
- options of a select 


In my implementation, each clickable link has a data-ajax attribute indicating the relevant ajax uri.
Same for the options: each option has the data-ajax attribute too.

By intercepting clicks and change events on attributes, I obtain the desired effect.



Product details
-------------------

With the need for more complex products comes product details.
Product details are like an extension of the attribute system.
It's better explained in the product-details.md document.


In terms of implementation, we don't need to worry about the token, since this is handled by the cart's 
**addItem** function already.


That's good news; the only thing left for us to do is collect the major/minor product details.
We will also collect a name (i.e. identifier), to make it easier for modules (or other providers) 
to recognize their own parameters and act on them.
 
We will provide a hook for providers (i.e. modules) to provide us with their product details.




Putting the implementation together
--------------------------

So now that things have been clarified, it should be easier to create something.

The main idea is that we can handle the "add to cart" button.
By collecting the product details if any, we can call the cart's addItem method with the adequate parameters.

This should entice modules to use this system, and thus improve the general consistency of the 
ekom "add to cart" mechanism. 




Virtual Quantity
----------------

Oh, and I forgot about an important area of the product box page: the virtual quantity box.

This box can be to some degree automated.

The basic idea is that the virtualQuantity is part of the ekom boxModel.

The formulae remains the same for most cases:

- v = sq - cq  (virtual quantity = stock quantity - cart quantity)

We have four level of products now:

- products with no attributes
- products with attributes
- products major details only
- products major details and minor details


The quantity (stock quantity) is already present for product with/without attributes.
It is assumed by ekom that modules providing complex products always set the quantity to a consistent value.
In other words, the stock quantity is there at all levels.

So, can we guess the cart quantity?

Let's look at the formulae generating the token:

- token = hash ( majorProductDetails )


Hmm, we need the major product details.
Those should be provided by the boxModel, via the productDetails property:

- productDetails:
    - major: array of name => item.
            The name is the name of the major product detail. 
            Suggestion: modules, consider prefixing your keys with namespaces,
            as this is a pool of variables shared by all modules.
            Each item being an array with the following entries:
        - nameLabel: the label for the name             
        - value: the value for this product detail
        - valueLabel: label for the value of the product detail
        - isSelected: bool, whether or not this product detail has been selected by the user
    - minor: free array (i.e. not normalized yet) of variables helping providers (modules or other things)
            to display the product minor details.
            
            
With this particular structure ready in the boxModel, we have access to the major product details of the 
current product instance, and thus generate the cart token.

With the cart token in our hands, we are able to access the necessary cart quantities and therefore compute
the virtual quantity.


Note: this is the approach for most cases, however experience has already proven the existence of products
that don't fit this model. For such products, they need to "manually" provide their virtualQuantity number.


             
                                            
                                    





































Side notes
==================

About the service called
-----------------------------

When you call the ajax service from the template in order to update the current 
page, you can either:

- call json data, inject the data with js
- call html data and inject the data directly
- there are other options like using a framework that makes bindings (angular, react..),
        but in ekom I won't use them (they are too fancy for me, and no time to investigate
        at this time of the year)



Calling json data is what I did in my previous attempts,
but it requires that you make the view twice.
The one that you cannot avoid is the static view, generated by php.
The second one is the js template: you need to recode part of the static view
that you want to change.
In some cases, although not hard at all, it's time consuming.

The html option was appealing, injecting directly the html fragments in the view.
The obvious benefit is that you can then factorize the generator of the view fragments.

One drawback is that to display something we need to make an extra ajax call which
is not necessarily required with the js technique (thinking about the update cart area).
So, I won't probably use it, since I prefer performances over a longest development time.

But here is my two cents about the html technique though.

In my mvc implementation, js comes from the view, so js belongs to the template.
In other words, to me the gui behaviour of a page is view dependent.

In other words ajax calls are just another tool in the view's arsenal.
And so using json or html is just a matter of tastes.

That's it for my alibi for using html, now to the implementation.


### Implementation

To make things easier for me (and everybody following my steps), I will create the following
convention:


- a theme that uses this technique of fetching html fragments should put all its fragments
    in a fragments directory, direct child of the application root directory
    
- then we can call the module's html service to access it


So the directory structure should look like this:

```txt
- app
    - fragments
        - $ModuleName
            - $fragmentId.php
    - www
```


The fragmentId is the identifier to the html fragment generator script.
It can contain slash for organizational purposes.


Then, the call to the html service is actually a feature provided by the Core module of kamille.
If you look at the **class-modules/Core/doc/systems/ajax-service.md** document, 
you will see how this technique originated.

Basically, the structure looks like this:

```txt
- app
----- service
--------- $ModuleName
------------- $type    // type can be one of html, gscp, json
----------------- $serviceName.php     // here I suggest serviceName=fragment

``` 

So, I recommend this structure:

```txt
- app
----- service
--------- $ModuleName
------------- fragment.php
```

The parameters will be:

- fragment: the fragmentId
- ...all other parameters are passed directly to the fragment generator script

That's it for the convention.




Couple of notes for the implementors
--------------------------
If you are using jquery, you can use $.get to fetch data, and inject it in your page.
The js code inside script tags will be executed (I tested in firefox with jquery 3.2.1).

However, if you wrap your code into a js event like this:

```js
document.addEventListener("DOMContentLoaded", function (event) {
    // not executed, because the event is not fired again when you import the script
});
```

The code inside the function won't be executed since this particular event is not fired
when "loaded/imported".

However, this does the trick:


```js
$(document).ready(function () {
   // this code is executed when imported :) 
});
```

 













