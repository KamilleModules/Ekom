Product box modifiers
========================
2018-04-23


https://www.merkleinc.com/blog/ecommerce-seo-product-variations-colors-and-sizes




What's a modifier?
--------------------
Let's start by a definition:

in Ekom a product box modifier is a "characteristic" of a product that can change the uri.



Ekom has currently two types of modifiers:

- attributes
- product details


So it means that if you change the size attribute (for instance from size XL to size XXL), then the uri can be changed,
or at least, ekom has an unique uri for that.


This distinction is important because for some other "characteristics" of a product, it wouldn't make much sense
to allow them to change the uri.

For instance for things related to a product's configuration: if you could sell the different letters (a, b, c) 
of a pc keyboard by set of 10 letters for instance, then it wouldn't make much sense to expose the concrete letters
that the user chose in the uri.


So, the product box modifiers are those "characteristics" for which it makes (common) sense to have a unique uri.




What does it look like in the template?
------------------------

Visually, a modifier could be anything, but most of the time it is one of the following:

- a select box
- a rows of buttons









About the implementation
----------------------------
In terms of implementation, I can see at least two possible implementations:

- the easy one
    passing all attributes/product details values to an ajax service which would resolve those modifiers to a single reference,
    and displaying the appropriate product page.
       
- the heavy one
    another ajax service is passed the reference directly, meaning the product box page has created all possible combinations
    of uri for the attributes/product details modifiers.
    It starts to become complex in the mathematical sense (at least from my analysis, but I'm not good at maths...).
    
- the current implementation:
    doesn't take into account the product details, so it's not valid, but I just wanted to point out that it handled
    something else, which might be interesting: the available quantity for each attribute variation, and whether or 
    not the product is active or not.    
    

Both approaches produce almost the same result, 
but the heavy one creates less variety in the uris created (since there is only one param: refId vs any number of params).


Anyway, both approaches primary goal is to provide a unique referenceId (and thus seo uri) for every possible 
modifier combination. 

   







