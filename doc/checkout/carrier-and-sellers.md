Carrier and sellers
======================
2017-12-03





Multiple sellers imply multiple invoices.

But what about carriers?


In current ekom version, we ship only to one address at a time.
By extension, this means we use pay the carrier only once (not saying it's the only possibility, but that's the model
I chose for ekom for now).

And also, the customer need only to set ONE physical address for the delivery.


With this context, when an order contains multiple sellers, this means that all sellers SHARE the same carrier.
This will translate later on the invoice, imagine seller A and seller B, with a total of 1000€ for the order, 
and a shipping cost of 100€:


Seller A invoice
------------------
- total: 700€ TTC
- shipping cost: 70€ TTC ( 70% of the shipping cost for this order)

Seller B invoice
------------------
- total: 300€ TTC
- shipping cost: 30€ TTC ( 30% of the shipping cost for this order)



In other words, the shipping cost (in current ekom), is bound to the order in the first place.
Then, it is redistributed on a per-invoice basis, depending on the involved sellers.
 

   
