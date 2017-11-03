Ekom product box 
=================
2017-11-03


The product box is the object used to display a product page in ekom.

The idea behind the product box object is that it can be used any time we need to display a product or a card.

- on a product page
- on product page refresh via ajax
- on a product list page
- on the product list in the mini-cart
- on the product list in the cart
- on products carousels
- ...basically every time you want to display a product and/or a card


This way, displaying a product or a card is a no brainer: we just use the product box.



The need for performance
===========================

However, there is a challenge: the product box has to be accessed quickly, because the last thing we want 
is the users waiting too long for a product/product list to be displayed; but the computation of the product box
is expensive by nature (because of all the potential information it needs to gather).

What we don't want is the application to feel slowish:

- the user clicks an attribute on the page refresh feels slow   
- the user wants to display a list but she/he has to wait more than 1 sec   
- ...all those kind of things 


As a workaround for this problem, the product box resorts to caching.



Caching
==============

The caching operation comes with its own challenges too.

As you know, caching involves finding an identifier and binding some data to it.

The simplest/naive approach is to say:

- why not take the product id and make it the cache identifier?

Would this work?
For the most part it would, but not for two things (from what I know):

- discounts
    It's possible that discount depend on time: for instance a discount which would only apply from 2017-10-01 to 2017-10-07
    It's possible that discount depend on the user's data: for instance a discount which would only apply if the user 
            is registered as a professional 
    It's possible that discount depend on any condition in general, at least technically (I don't have concrete example, 
            so let's not make a big deal about this one, but bare in mind that it's there)
- taxes 
    It's possible that a tax applies only depending on user's data: for instance a tax that applies 
    depending on the shipping address, or a tax depending on the user's living country (both cases I had in my company) 


So, we see that a few non-cache-friendly things appear here:
- time
- user data (user country, shipping address, ...maybe other things)


Does that mean that we cannot cache the product box?
Not necessarily, but we need to be cautious with what we do.


Ekom approach
----------------

The subject of displaying the product box as fast as possible is still an open debate,
but the current ekom approach for caching the product box is the following:


- collect the product box context  
- call the cache 



The novelty in this approach (compared to the previous ones I did) is the new concept of the **product box context**.

Simply put, the **product box context** is the ensemble of data that allows ekom to compute a unique 
product box model, such as the string version of the **product box context** (i.e. a hash for instance) becomes 
a suitable cache identifier for the product box (and that's the whole point of this concept).
 
The **product box context** is composed of two parts:

- the native context data 
- the modules context data

The **native context data** is the part of the **product box context** brought by the ekom module, while the
**modules context data** is the part that comes from other modules.
  
The **native context data** contains the following:

- ?shop_id:
- ?lang_id: 
- product_card_id: int
- ?product_id: null|int
- ?product_details: array
- ?date: 
- ?group_ids: array ordered by id asc
- ?currency_id:

Notes:
- the product_card_id is the only property which needs to be set by the developer,
    the other properties are either optional or can be guessed by the class
- the properties with a question mark in front of them are optional,
        and are automatically set by the class if not set by the developer.
        

 

  
And the **modules context data** obviously depends on the modules being used.
Here is an example of what data we might find in the **modules context data**:

- user_country: BE              (brought by ThisApp module)
- user_shipping_country: DE     (brought by ThisApp module)
- b2b: 1                        (brought by ThisApp module, values can be 1 or 0)



Using this concept of **product box context**, we can come create a powerful cache identifier.
Such a complete cache identifier does two things:

- pro: it is possible to cache the product box **INCLUDING RELEVANT DISCOUNTS AND TAXES**
- coin: it multiplies the number of cache stores: basically each product 
        has potentially ($nbCountry x $nbShippingCountry) stores per day 
        
        
So the benefit is obvious: we CAN cache the product box and access it lightning fast.
Now the drawback is that it makes it harder to hit the cache, since different users will bring different parameters.

As a workaround, we can consider the option of creating the most used cache targets in advance, using a cron task.

         



Implementation synopsis
==============


Legend
---------
- op: original price, the price set by the shop owner (no tax applied)
- bp: base price, the price with applicable tax(es) applied to it.
            The term applicable refers to the fact that the tax(es) might be different from an user to another
            (depending on the user shipping address for instance). 
            If no tax applies to the product for a given context (**product box context**), then bp=op.
- bp0: base price without tax.
            In case the shop just wants to display the base price without tax
            In case the shop just wants to display the base price with tax
- sp: sale price (aka discount price): the base price with applicable discounts applied to it.
            Ekom collects the applicable discount procedures, it does not apply the discounts directly.
            
            That's because applying discount procedures is cheap (it's just some basic math like addition,multiplication...)
            and is more flexible.
            
            Applicable refers to the fact that a discount might apply/not apply depending on time, user data, 
            or even other parameters (but still provided in the **product box context**).
            This means that the **product box context** provides all we need to decide 
            WHETHER OR NOT A DISCOUNT APPLIES.
            
- sp0: sale price without tax
            In case the shop just wants to display the sale price without tax.
            We just apply the discount procedures to bp0.
            


Note: applying taxes is cheap, much like applying discount procedures.


### Technical details
 
- discount procedure:
    - procedure_type: amount|percent
    - procedure_operand: a number

            


Synopsis
-------------

[![product-box-price.jpg](https://s19.postimg.org/dql2enlc3/product-box-price.jpg)](https://postimg.org/image/w65jc1zgf/)

The shop owner fixes the original price (op).

Inside the object providing the product box:
- collecting the **product box context** 
- inside the method that returns the product box model (i.e. array) from the **product box context**: 
    - get op from the database
    - computing base price (bp)
        - define applicable taxes (with the **product box context** available) 
        - bp = apply_applicable_taxes(op)  
        - bp0 = op  
    - computing sale price (sp)
        - collect applicable discount procedures (with the **product box context** available) 
        - sp = apply_applicable_discount_procedures(bp) 
        - sp0 = apply_applicable_discount_procedures(bp0)
     



More technical implementation details
----------------------


### The cache

The ProductBoxEntity object is used, and has a getModel method.

Since we use tabatha cache system, we actually need two things before we can cache the product box model:


- the cache identifier
- the delete identifiers (specific to tabatha), which are the items of the array passed as the third
        argument of the tabathaCache get method


The funny thing about those identifiers is that they need to be provided BEFORE we have any information
about the product (except for the card id).

So that's a chicken-egg problem.

To deal with that, modules just pass all the delete identifiers they can possibly use to create the
product box model.

Therefore, the common thing between the cache identifier and the delete identifiers is that their data don't
depend on the product.

That's why we use only one hook (because a hook is not the cheapest operation) to gather both of them.

This hook is called Ekom_ProductBox_collectPreCacheData.
It is called before the cache is invoked and receives one array passed by reference as its sole argument.

Then the ProductBoxEntity will respond to the following:

- modulesContextData: array of key => value representing the **modules context data**
- cacheDeleteIdentifiers: array of values (each value being a cache identifier)


### The hybrid system: calling the Ekom_ProductBox_collectPreCacheData hook

In ProductBoxEntity, the two properties cacheDeleteIdentifiers and modulesContext are initialized with null instead of 
an array.

That's because when the model is requested, if a value is null, the hook is called internally.
Otherwise, it's assumed that the hook has been called externally.

It should be called externally when list of product box models are requested, thus allowing for the following interesting
design (bare in mind that fast access is our main concern):

```php
info = ProductBoxEntityUtil::getCacheContext(); // easier than calling the Ekom_ProductBox_collectPreCacheData hook manually

allBoxes = [];
list = []; // ...
foreach(list as item){
    allBoxes[] = ProductBoxEntity::create()
    ->setModulesContextData(info[modulesContextData])
    ->setCacheDeleteIdentifiers(info[cacheDeleteIdentifiers])
    ->getModel();     
}
```

In other words, the **Ekom_ProductBox_collectPreCacheData** hook is called only once for the whole loop.
Yet if we need a single item, like for instance if we want to display the product box page, then
we don't need to call the **Ekom_ProductBox_collectPreCacheData** hook manually, ProductBoxEntity does it 
automatically for us.


 
### Taxes

Taxes naturally come from the database.
But then, we decide whether or not to apply them.

For instance if the user's shipping_address is in germany, we can decide that he/she will not pay taxes.

However, in ekom taxes are bound to a product using tax_group.
So, a group could potentially PARTIALLY being discarded (i.e. the first tax of the group might apply but not the second).

To accommodate this constraint, we simply use a php array (which is very flexible) and pass it to the modules using a hook:

```php
$config = [];
Hooks::call("Ekom_ProductBox_filterTaxGroup", array &$config, array $productBoxContext, array $productInfo);
```

Then, after this call, we will treat the following keys if found:

- noTax: true, will skip the whole tax group. This is the only case we will be using in my company
- ...you own extensions here



### Discounts


With this system, discounts are cached within the product box.
That's for how long as the discounts system can resolve the discount conditions (whether or not a discount
applies) using only the **product box context**.

This is the case in our conception: the ek_discount table has the following fields pertaining
to conditions:

- user_group_id
- currency_id
- date_start
- date_end
- condition

All of those fields encode conditions which can be resolved using the **product box context**.














 









