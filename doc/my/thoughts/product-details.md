Product details
===================
2017-09-18



Product details is an extension of the ekom attributes system, it characterizes a product instance.

In short, a unique combination of attributes gives us a product id.
A unique combination of a product id and product details gives us a **product instance**.

The product instance, in the cart, is given an unique string called **product identity**: the 
**product identity** identifies a product instance.


Product details can be passed via the uri, and the product box page reacts to those.
In other words, we can either control the gui details control using the mouse, or by passing
the parameters in the uri (or both).



The product identity
-----------------------

The **product identity** identifies a **product instance** in the cart.
Whenever the user interacts with the cart, the **product identity** is used.
So, adding a product in the cart, removing a product from the cart, or updating a product's quantity
in the cart, all those operations require the **product identity**.



Token
----------

By default, the **product identity** is a hash based on the product details.
However, in some cases using this default hash might not be the most desirable solution.

That's why ekom provides the token system.
The basic idea is that the hash is based on a stand alone number, like the timestamp for instance, rather
than on the product details.

Why is a token useful?

When the items are displayed in your cart, there is an "update link" pointing to the product instance,
and allowing the customer to update her product.
In some cases, it's not even possible to update the product if we base our **product identity**
on the product details, because the details might change as the user updates the gui, but the
product instance that we want to update in the cart is the same. 



