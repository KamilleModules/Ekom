Contextual event enhancement
===============================
2017-09-26



Intro
=======
So a page contains a number of js custom events ready to fire.

For instance, if the user changes the quantity of item#1 from 1 to 2 in the minicart,
a "cart.updated" (from ekom js api) will be triggered.

All subscribers to the cart.updated event will then receive the cartModel.

This system works generally fine, because then armed with the cartModel, the template author
can update her view accordingly.

But what if the data in the cartModel is not practical to work with, what if 
the (to some degree lazy) template author wants more data to be provided with the cartModel?


That's the idea behind contextual event enhancement.



Implementation
================

This implementation has to be done on every ekomApi function that you wish to "enhance".

The designed function will basically use a hook to collect extra params that will improve the function.

Generally, the function transmits the extra params to a server side service which usually makes 
an useful (hard to reproduct on the front side) computation. The result of which being passed back to the caller,
thus the term "enhanced".



Some services in the ekomApi arsenal benefit this system, notably the api.cart.updateItemQuantity method.

Note: enhancement of api methods is done as we discover the need for them.



The cart.updateItemQuantity triggers the following hook to collect params:

- collectParams.updateItemQuantity








