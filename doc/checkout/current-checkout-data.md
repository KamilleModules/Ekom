Current Checkout data
=================
2017-11-09



The currentCheckoutData is THE (de-facto) accessor to the current checkout data, which are:

- started: bool: true if the checkout process is currently running, false otherwise
- carrier: the name of the carrier used; null  
- shipping_address: 
- billing_address
- payment_method
- ?...possibly other properties added by modules 



All values will return null if the checkout process isn't started,
except the started property which always return a boolean.


It has a simple get/set method pair.


The currentCheckoutData uses the php session to store the data.

The session bag is cleaned/flushed whenever the checkout process is completed (i.e. the user has purchased the items).

The session bag is filled up with the default values whenever the user enters the checkout process with an empty 
session bag (i.e. when the user enters a new checkout process for the first time).


