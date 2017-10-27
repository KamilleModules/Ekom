Ekom product box model
=================
2017-10-27



The ekom product box model (pb model) is an array representing a product in ekom.


This is probably the most used model in ekom, because it is such a central piece.
It is used as the base for building most product lists.



One of the challenge of building the pb model is to be able to cache it.
However, the pb model can depend on many factors, including user data.

So, to read all the details of how we do it, continue reading...



The product box model structure
===================================

The product box model can have at most two main parts:

- the primitive part
- the dynamic part



The main idea is that the primitive part contains the product data coming straight from the database,
and the dynamic part contains the data that cannot be cached:

- the discounts (since discounts applying conditions are theoretically infinite) 



The ekom productBoxDiscountMode configuration property defines which parts are used





The product box context
===============================

The product box context (pbc) is an array representing the ensemble of data necessary for the 
ProductBoxLayer.getProductBoxModelByCardId method to return the desired/personalized product box instance model.

The ProductBoxLayer.getProductBoxModelByCardId method being the only producer of the boxModel in ekom (by design, 
so that it's easier to make the model evolve).


The pbc's secondary goal is to be turned into a string, which then can be use to cache the model (i.e. motivation
for creating the pbc in the first place was to be able to cache the product box model).


The pbc contains:

- info allowing to decide whether or not the tax applies
        - the product id, which gives us indirect access to product info such as the seller
        - the user country, or 0 if the user is not connected
        - the user shipping address' country, or 0 if not connected
        
- info allowing to decide whether or not gathered discounts apply 
        - the user group id
        - the currency id
        - the current date

- whether or not the user is connected? not sure yet about that one
 
 
 
Note that it would be inefficient if we had the userId as part of the caching string, as we would need a personalized 
cache per user. 
 





