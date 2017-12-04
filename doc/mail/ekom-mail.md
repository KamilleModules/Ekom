Ekom mail
==============
2017-12-02


Ekom mail strategy is based on a simple pattern: you call only one function: sendMail,


```php
E::sendMail ( code, array params=null )
``` 


and you pass a code indicating the type of "prebaked" mail that you want to send.

If the mail needs to be configured with some parameters (for instance, the userId),
then you use the second argument: params to pass them.



This pattern allows us to quickly dispatch mail (from the dev code), but it relies on a strong convention:
which codes and which parameters.




Ekom Mail Convention
==========================


The at feature
--------------

Ekom uses the at feature for codes.
A code starting with "@user." is a mail intended for user.
A code starting with "@stuff." is a mail intended for stuff.
A code starting with "@admin." is a mail intended for admin.

etc...
You can create your own prefixes.
Ekom uses the following prefixes:

- @customer: message intended for the customer
- @com: message intended for the commercial service of the company
- @dev: message intended for the developer team


A dot separates the at prefix from the rest of the code.

Example: @customer.orderConfirmation





Below is the list of codes and params used by ekom.
If your app/modules send more mails, you can extend this list

- (work in progress) 


