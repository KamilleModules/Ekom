Ekom statuses
=================
2017-09-27



[![ekom-status.jpg](https://s19.postimg.org/j65eybc2r/ekom-status.jpg)](https://postimg.org/image/n2iquax27/)



The statuses are the following:

- phases (from yellow to blue)
    - payment_sent: FFFF00
    - payment_accepted: 80FF00
    - payment_verified: 00FF00
    - preparing_order: 008080
    - order_shipped: 29ABE2
    - order_delivered: 0000FF

- errors (from red to orange)
    - payment_error: FF0000
    - preparing_order_error: F15A24
    - shipping_error: F7931E
    - order_delivered_error: FBB03B

- problem management (from indigo to purple)
    - canceled: 9E005D
    - reimbursed: 662D91





Errors
	Those are accidents that come in the way of delivering a good product to the customer.
	Usually, an error is a temporary status which resolves to a status from the “Problem management” category.
Phases
	Those are the statuses that occur in a perfect sell.
Problem management
	Those are statuses for managing the various problems that may occur during the sell.
Payment sent
	The customer has initialized the payment.
	This means she has agreed to pay by completing her order on the online store.
Payment accepted
	Only for payment methods that use a webservice to check immediatly if a transaction should be accepted or not.
	“Paypal” and “Credit card” payment methods typically use this status.
Payment verified
	The merchant (or an automated system on behalf of the merchant) has verified that the money has been transfered to the merchant bank account.
	From there on, the store can send the merchandise to the customer.
	Note: the merchant could also decide to send the merchandise while in the “Payment accepted” phase if she wanted to, but it’s not recommended.
Preparing order
	The payment has been verified (or accepted) and the merchant is actually preparing the order for shipping.
Order shipped
	The order has been sent, it’s on its way to the customer.
Order delivered
	The transporter  has reported that the delivery was successful.
	Note: the merchant could then send a mail asking the user to confirm the delivery and report any problem (missing products, damaged products).
Payment error
	The payment couldn’t be verified.
Preparing order error
	An error occuring during the “Preparing order” phase.
	For instance, the store doesn’t have the product anymore.
Shipping error
	An error occuring during the “Order shipped” phase.
	For instance, the transporter failed to deliver the order because of an inexisting address.
Order delivered error
	An error occuring during the “Order delivered” phase.
	For instance, the customer called the after-sales service to report a damaged product.
Canceled
	Indicates that the order was not sent to the user.
	This is a final state (i.e. it cannot evolve).		
Reimbursed
	Indicates that the order was partially or totally refund.
	This is a final state (i.e. it cannot evolve).		