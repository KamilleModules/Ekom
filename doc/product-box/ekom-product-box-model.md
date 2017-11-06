Ekom product box model
=================
2017-11-06



The ekom product box model (pb model) is an array representing a product in ekom.


This is probably the most used model in ekom, because it is such a central piece.
It is used as the base for building most product lists.




One of the challenge brought by the pbox model is to be able to cache it.

As discovered in the **database/refuting-the-idea-of-sale-price-in-the-database.md** document, 
the sale price depends on many factors, such as the date, or even worse when it comes to caching: user based data.


Does this mean we need to create a cache for every combination of factors that defines the sale price?
In ekom, that's the approach we took.


In terms of implementation, creating the box model then looks like this:



It starts with the GET params, which indirectly or directly encapsulates the following elements:

- the product card id
- the product id
- the product details


The product card id and product id are guessed by ekom.
The product details have to be collected by modules.

Ekom will provide a hook so that modules can extract the relevant product details from the GET params, 
as we don't want unnecessary/error-prone parameters to be part of the product details.



Then, Ekom also uses TabathaCache system, which asks for some **delete cache identifiers**.
Ekom needs also to provide a hook so that modules can declare the **delete cache identifiers** necessary to make
all this system work.


Also, modules need to build the **ekom product general context** (see the **ekom-product-box-context.md** document 
for more information).
And so Ekom also needs to provide a hook for that.



Then, we enter the part of the function that will be cached.
This is a three steps process:

- build primitive model
- call the decorateModel hook
- resolve the price chain


Build a primitive model
--------------------------

We basically collect anything we can including the original price (set by the shop owner),
and the applicable taxes and discounts.

At this step, the taxGroup and discount properties are collected, but not applied yet,
because the next step can change them.




Call the decorateModel hook
--------------------------

The primitive model can be decorated by modules.
That's the purpose of the decorateModel hook.
Often, modules that use the **product details system** will add
variables to the primitive model that will be interpreted visually in the view.

With this hook, modules also get the opportunity to change the original price (raw version)
(for instance if the price changes depending on the product details) or any other property
of the primitive model.

Note that this happens before the "price chain" is resolved.

Modules can also express their intents with this hook (which might be resolved later by the ekom method, although
this system is currently not implemented).

Things like changing the tax group on the fly become possible.




Resolve the price chain
-----------------------

Once this is done, a big line is drawn and we move to the last section.

We now consider the original price as definitive (modules had their chances to express their intents and decorate/update
the model how they wanted). 

Now it's time to resolve the "price chain".

In other words, commit our taxGroup and discount decision.
 
This is a standard ekom procedure.

The "price chain" resolving process takes the original price, the applicable taxGroup (if any), 
and the applicable discount as input, and computes the base price (plus its with/without variations), and 
the sale price (also with its with/without variations).

Note: the applicable taxGroup and applicable discount were prepared by modules in the previous step and cannot
be modified at this moment (they are readonly values).







So to recap, here is how it looks like visually:


```txt
     
     
    (this part is optional, and occurs if we want to "extract" the product details from the $_GET array) 
    GET
    --------------------------------Hooks:Ekom_ProductBox_extractProductDetails
    
    
    
    
    
    
    (We need to run this line before the cache function is entered)
    --------------------------------Hooks:Ekom_ProductBox_getTabathaDeleteIdentifiers
    
    --------------------------------?Hooks:Ekom_ProductBox_collectGeneralContext (only called if the general context 
                            is not already provided)
    return A::cache()->get(  
            
            
            Ekom primitive model
            
            
            --------------------------------Hooks:decorateModel (intents, change original price, change quantity, ...)
            
            
            Price chain resolution
            
    )
    
 
```
                    
                    




















 





