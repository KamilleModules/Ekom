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

Ekom discounts conditions language (edcl) is a simple language for writing whether or not
a condition should apply.

Edcl is designed to be written/read from a database, and it is human readable (i.e. not serialized)
so that the administrator can change it directly inside the database if she/he wanted to.


The language is just a big string representing a condition.

In the process of displaying a product, ekom evaluates this condition (which returns a boolean)
to determine whether or not the chosen discount (if any) applies to the price of the product.

The language has two elements:

- comparison block
- logical operator


The comparison block is a statement resolving to a boolean value.

It generally involves two values separated by a comparison operator, but it can also be composed of 
only one value (mainly implemented for testing purpose), or three values in the case
of a comparison block using the between comparison operator.




The available comparison operators are:

- = (equivalent of php ===)
- != (equivalent of php !==)
- < 
- <= 
- > 
- >= 
- >< (between exclusive, then this comparison operator expect two arguments separated by a comma,
        whitespace around the comma has no meaning) 
- >=< (between inclusive, same as ><, but accepts boundaries)


The values can be any value, or a variable (using the dollar symbol as a prefix).
Variables are replaced by their corresponding value in the **ekom discount context** array,
which is as for now exactly the same as the **ekom product context** array explained
elsewhere (in description of product/product list).

Note that this **ekom discount context** might be extended in the future. 


So for instance, this is a typical comparison block:

- $lang_id=2


Each comparison block is in itself a statement that can be evaluated and returns a boolean.

The logical operators are the following:

- ||
- &&
- (( 
- ))

With them, we can combine multiple comparison blocks together.

- $lang_id=1&&user_country=FR
- ((1 && 2)) || 3
- 1 && ((2 || 3))
- Note: 1 && 2 || 3 is equivalent to 1 && ((2 || 3))
        In other words, the condition string is first cut with &&, then || 


Note that **ekom discounts conditions language** is a primitive language and does not allow
for nested logical groups.









