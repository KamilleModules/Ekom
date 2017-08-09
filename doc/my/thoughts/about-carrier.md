About carrier
==================
2017-06-14




The available carriers first exist as objects in the system (class-modules/Ekom/Carrier/CarrierInterface.php).

Then the available carriers for a given shop are defined by two things:

- first, their presence in the ek_shop_has_carrier table
- second, the carrierSelectionMode directive, which can be at least one of: fixed, manual, auto



In case of fixed or auto, the user is not involved and it eases the estimate shipping cost process (which occurs
on the cart page), and also the checkout process, as less steps are involved.


If manual mode is chosen:
- to estimate the shipping costs we basically run through all the available carriers for the shop,
and use some algorithm to select the carriers that are going to handle the products.
- on the checkout page, we provide the user with the choice of the carrier



