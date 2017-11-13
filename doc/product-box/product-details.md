Product details
===================
2017-11-09



Product details is an extension of the ekom attributes system.

Whereas every attributes combination leads to a unique product reference, product details allow us
to go even deeper and reflect more complex products.






Why product details?
======================
 
With the attributes system, t-shirt X with red color and size small has reference 123,
but t-shirt X with red color and size medium has reference 152.

Now what happens if we sell a training course.
The product is training course X, but we can choose the date.

One strategy would be to create one product reference for every date.
However in ekom, the default is to consider that this is the same product (and reference),
and only the details change.
That's because then it's a little bit easier to make stats on a reference.

So in ekom, training course X 2017-11-09 has reference 1234, and training course X 2017-12-05
also has reference 1234 (assuming that the date was defined as a product detail).

However when you add training course X 2017-11-09 and training course X 2017-12-05 in your cart,
you have by default two different items.


Ok, but we can go even further.

Imagine we sell a composable keyboard and each key is an option.
So for instance you can buy the composable keyboard CK with letters a, z, e, r, t, y.
Or, you can buy the composable keyboard CK with letters, a, r, t.
Or any combination...

In ekom, this is called a configurable product.
A configurable product is a product which configuration doesn't change the reference (much like the training course X),
but also replaces its own quantity when added (just added, not updated) to the cart.

You might wonder why that is.
That's because when you review your items in your cart, there is an update button (in case you change your mind).
When you click the update button, it redirects you to the product page again (with the classical "add to cart" button).
So imagine you're reviewing your cart, and you have the composable keyboard CK with letters a, z, e, r in your cart, 
but now you change your mind and want to switch to a CK with letters z, t, y instead.

So, you click the update button and land on the CK product page.
Then, you turn off all options except for the z, t and y letters.
Then, you click on the add button.

And now, because it's a configurable product, ekom will replace the azer CK keyboard in your cart 
with the zty CK keyboard.

If it wasn't replacing the quantity, you would end up with two items:

- azer CK keyboard 
- zty CK keyboard

which ekom assumed this is not what you wanted (since you wanted to update the item).

So now you have one zty CK keyboard in your cart.
Note that if you change the quantity to 2 (or any number) before adding the item to your cart, the quantity replaces 
the old one (but is not added to the old quantity like it normally does on non-configurable products).
 
Now if you want 3 zty CK keyboard, you can just use the update quantity buttons, those work the same as for other products,
they just update the quantity without distinction on whether your product is configurable or not.

 
So to recap, 

- with the attributes system every attributes combination leads to a unique reference
- with the product details system, we have two modes:
    - non-configurable products, the product details do not change the reference, however they lead to 
                        different items (with different details) in the cart
    - configurable products, the product details do not change the reference, and when you click 
                        the "ADD TO CART" button, they instead replace the configuration of your current item in the cart 
                        by the new configuration, and the quantity replaces the old quantity (i.e. it's not added)  





Product details implementation in ekom
======================

Product details are such as if you pass the product details in the uri, 
the product page will reflect them no matter what.

(This feature is necessary so that a user can browse her purchased products history and land
on the desired product pages if necessary.)



The product details have three forms:

- product details args: this form is the ensemble of product details passed via the uri
- product details array: this form is an array, created by concerned modules, which contains the following structure:
        - major: array key => value representing the major product details (product details representing a non-configurable product)
        - minor: array key => value representing the minor product details (product details representing a configurable product)
- product details map: an array of key => value.
                    This is useful to inject them as we want in the template.



We can divide product details in two groups:

- major product details (non-configurable products)
- minor product details (configurable products) 



Product box
-----------------

The product details system is reflected throughout the product box model via three properties:

- productDetails: the product details array
- productDetailsArgs: the product details args
- productDetailsMap: the product details map


Cart
----------
In the cart, major product details are combined with the product id to product the token (see cart.md for more info about token).
Which means two products with different major product details lead to two different items in the cart.

Minor product details DO NOT provoke a change of the token.

The **product details array** is stored in the cart item with the property: details.
If the product doesn't use the product details system, the key doesn't exist.

So, a typical item using product details in the cart looks like this:

- ($token)
    - id: the product id 
    - quantity: the cart quantity
    - details: the **product details array**
    
    
    
    

