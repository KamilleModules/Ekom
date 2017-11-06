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


The comparison block is a statement involving at least two values and a comparison operator.
The available comparison operators are:

- = (equivalent of php ===)
- != (equivalent of php !==)
- < 
- <= 
- > 
- >= 


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

With them, we can combine multiple comparison blocks together.

- $lang_id=1&&user_country=FR


Note that **ekom discounts conditions language** does not allow to make logical groups yet (using parenthesis
to make groups).











 





The discount object
----------------------
In ekom, a discount is represented by the two tables:


- ek_discount
----- id: pk
----- procedure_type: str
----- procedure_operand: str
----- target: str
----- shop_id: fk

- ek_discount_lang
----- discount_id: pk
----- lang_id: pk
----- label: str



### Type
A discount also has a type, which can be one of:

- product: applies at the product level
- cart: a discount applying at the cart level
- ...(your own types)

The type is inferred by the context.


### Target
The target is the type of number on which the discount applies.
In ekom, in the context of there are different possible targets depending on the discount type.

@todo-ling: remove deprecated targets, and update this document' section.

- product level discount
    - beforeTax 
    - afterTax
    - tax: use the application's default between beforeTax and afterTax
    
    - priceWithoutTax (deprecated)
    - priceWithTax (deprecated)
    
- cart level discount 
    - shippingCost (deprecated)
    - taxes (deprecated)
    - ...



### Procedure

The procedure is HOW you apply the discount.
The procedure is itself composed of two elements:

- procedure_type: a technique identifier, currently:
    - amount
    - percent
- operand: an operand to work with, which signification and use depends on the procedure_type.
        For instance, if the procedure_type is amount, then the operand represents a fixed amount to retrieve from the 
        target price.
        On the other hand, if the procedure_type is percent, then the operand represents the percentage of the target
        price that needs to be removed.
- ...    
    
    
    

    
    
    
    
    

The binding
----------------
A discount can be bound to a product, a card, or a category.

This is materialized in the three following tables:

- ek_product_has_discount:
----- product_id: pk
----- discount_id: pk
----- order_phase: int
----- active: 1|0

- ek_product_card_has_discount:
----- product_card_id: pk
----- discount_id: pk
----- order_phase: int
----- active: 1|0

- ek_category_has_discount:
----- category_id: pk
----- discount_id: pk
----- order_phase: int
----- active: 1|0



The conditions
-----------------

There are two types of discounts conditions:

- database conditions
- filesystem conditions


The database conditions use the fields in the ek_discount table...

- user_group_id
- currency_id
- date_start
- date_end

... to decide whether or not the condition should apply to a product.


Although the database conditions system is useful, it certainly is limited and might not cover
all the website owner's needs.

Therefore, the filesystem conditions system was created.
The basic idea is that the condition can use the whole expressing power of the php language to return 
the desired boolean (whether the condition applies or not).

Note: the filesystem conditions system can hardly be cached, and therefore we generally prefer to use
the database conditions system when possible.
In fact, we prefer to extend the database model whenever possible, rather than using the filesystem,
because of this caching reason.
Note that the cacheString should contain a whole lot of information: the currency_id, the user_group_id,
the current date, and more if we extend it.




There is no current implementation of it, although I did something in the past.
My (old) code is still in this app (data/Ekom/conditions/products/1.php), and looks like this:

```php
<?php


$date = date('Y-m-d');


$target = "priceWithoutTax";
$condition = "date between 2017-05-25 an 2017-06-31"; // just for the user and gui
$condition = ($date > "2017-05-25" && $date < "2017-06-31"); // used concretely by the prod application
$procedure = [
    "type" => "amount",
    "operand" => "5",
];







$cb = function (array &$model) {
    $date = date('Y-m-d');
    $model['priceWithTax'];
    if ($date > "2017-05-25" && $date < "2017-06-31") {
        $model['priceWithoutTax'] -= 5;
    }
};
```


By reading all documents, you might even found a code that can process this file, but I'm not 100% sure if 
I've created this implementation or not.
Hopefully this helps.


