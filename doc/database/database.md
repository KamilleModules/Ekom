Database
==================
2018-03-14




This is a major revision of 2018-03-14-database.md.
The shop, lang, and currency concepts have been removed from the database for now.

Now the database has 55 tables, vs 81 tables in previous version.




Now, ekom is a virtual shop that allows us to sell items.


Important concepts
----------------------

- Shop vs store         (shop is virtual, there is only one shop now in this new approach; store is physical)
- Card vs product       (see previous docs)




ek_store
-----------
This is the physical store from which items are delivered.
They are used to compute shipping costs.

When the user buys an item, we should use the store the closest to her so that the shipping costs are minimum 
for the customer.



ek_timezone
-------------
The timezone used both in the front and backoffice.



ek_carrier
------------
Name of the carriers used by this shop.

- priority number: arbitrary number defining in which order carriers solutions should be displayed in the front.
                    The default carrier of a shop is the one with the lowest priority number.
                    
                    
ek_manufacturer
---------------------         

The manufacturer is the company/entity that creates the product.



ek_shop_configuration
-----------------
configuration vars for this shop.

- key
- value



ek_payment_method
-----------------
- configuration: text(serialized), some payment methods require configuration (for instance a paypal key, etc...)




           