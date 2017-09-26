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





