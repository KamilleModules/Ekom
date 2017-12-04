Order, invoices and payments
======================
2017-12-03 --> 2017-12-04


When the customer puts items in his cart, he doesn't have to be aware that each of those products are sold by an 
"administrative" entity called seller.


However, for legal reasons, each seller will provide the user an invoice.

Therefore, if the customer orders items from various sellers in his cart, he can potentially
receive (or at least generate) more than one invoice, one per seller.


Now in ekom there are two tables: invoice and payments.

What an human call invoice is actually (in ekom) the combination of a payment with the information
of the invoice.

An invoice is captured in at least one payment, but can be distributed amongst multiple payments.

- for instance, if the shop allows the user to pay in 3 times, we will have one invoice, but
        three payments
- or for instance if the shop requires the user to pay a deposit of 30% now and 70% later,
        in which case we have only one invoice, but two payments


In other words, we need to be aware that the same invoice can be re-edited multiple times, 
with just the payment numbers evolving. 
Legally, it's the SAME invoice, and for ekom, it's just different payments.





