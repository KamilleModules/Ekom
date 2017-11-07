Discounts
=============
2017-11-05


The ekom discounts system allows us to bind discounts to products.


There are three general steps:

- discount: creating a discount
- binding: selecting which products should benefit the discount
- condition: deciding whether or not the discount applies to the bound product



Creating a discount
========================

In ekom, a discount is a simple procedure applied to a number, which results in an other number.
The procedure is an object with the following properties:

- type: percentage|fixed
- operand: a numeric value


Those properties define the mathematical function to apply to the number.

The **type** defines how the **operand** should be applied to the given number.
In case of **percentage**, the operand represents the discount in percent.
In case of **fixed**, the operand represents the discount in the shop's currency.


In the database, the discount also has a **target** field.
The target is an abstract identifier used by ekom to know which price to apply the discount procedure to,
at the code level.



Binding the discount
=======================

Once the discount is created, we need to attach it to some ekom objects so that it applies to products.

The ekom objects a discount can be attached to are the following:

- product 
- product_card 
- category

Note that the above list is given from the most specific object to the least specific.


This means we can attach a discount to the product directly.

Or, we can attach it at the product card level, in which case all the products contained
inside the product card will be affected by the discount.

Or, we can get even bigger and bind a discount at a category level, in which case all products
of that category (including all children recursively) will be affected by the discount.

 
There is another rule: A PRODUCT CAN ONLY HAVE ONE DISCOUNT AT A TIME.

So, in case of conflicts, that is if the discounts that you set on different ekom object overlap
(for instance there is a discount on a product, and a discount on the parent product card too),
then the most specific ekom object wins.


Further more, if you have multiple discounts attached on the same product for a given level 
(let's say the product level for instance), then only the discount with the highest id will be used.
Note: maybe in the future ekom will support multiple discounts on the same product, but for now,
as to simplify things, we only use one (which is all my company needs anyway).



So, if the discount is set on a product object, then it will always win.
A product card discount will win against a category discount, you get the idea.



Binding the discount to ekom objects allows us to answer the question:
WHICH DISCOUNT WILL APPLY TO A PRODUCT?
 
 
And the answer doesn't depend on whether the condition actually applies or not, which is addressed in 
the very next section. 

In other words, the discount that might apply to a product is predictable.  
 



Discount condition: whether or not the discount applies
===========================================

Now that we know which discount to apply, we need to figure out whether or not the condition
actually applies to the target product(s).

For that, we use a language called "ekom discounts conditions language".


ekom discounts conditions language
------------------------------------

Ekom discounts conditions language uses the condition language 
from the [ConditionResolver](https://github.com/lingtalfi/ConditionResolver) planet.
The pool is the **ekom discount context** array, which is actually 
exactly the same as the **ekom product context** array explained 
elsewhere (in description of product/product list).







