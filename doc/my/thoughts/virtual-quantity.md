Virtual quantity
===================
2017-08-29 -> 2017-09-18



The virtual quantity is a number representing the maximum quantity of a product instance that a user can buy.
It decreases/increase as the user removes/adds product(s) to her cart.


The general formulae of the virtual quantity is the following:


- virtualQuantity = stockQuantity - cartQuantity






Explanations
=============

This is a concept I created to cope with displaying a product page's quantity.


Imagine we sell t-shirts.

We go on the ABC t-shirt product page, and it has 3 colors:

- red: 9 items left
- green: 200 items left
- blue: 50 items left


Our goal is to prevent the user to add more than 9 items in her cart.
Because if she does, we might have problem to deliver (depending on our stock strategy, but the default 
strategy in ekom is to consider the stock number as absolute, and so we cannot go below 0).


So, if the user adds 3 red t-shirts in her cart, the quantity is still 9 items left,
but the virtual quantity is 9 - 3 = 6 items left.


The virtual quantity cannot be negative.









 