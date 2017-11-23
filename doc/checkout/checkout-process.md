Checkout process
=====================
2017-11-23





Overview
=============

The checkout process is a helper for displaying the checkout tunnel.

The main idea is that the CheckoutProcess acts like a Registry (singleton): it's accessible at any time by any object.
Plus, it's stored in session.

Because of those properties, the CheckoutProcess can be updated from a static page and/or from ajax scripts,
which makes it quite flexible to work with.

This schema illustrates this idea of an always available CheckoutProcess:

[![ekom-checkout-process.jpg](https://s19.postimg.org/hcps8ik8j/ekom-checkout-process.jpg)](https://postimg.org/image/eimmv2i27/)





The static script idea
===========================

The idea, when using the CheckoutProcess inside a static script is the following algorithm (assumed inside 
a Controller):

```txt

model = []
CP = CheckoutProcess::create
if CP.isComplete 
    try
        CP.placeOrder ( function(){
            // on success, we redirect the user
            return SomeHttpResponse( "checkout_thankyou" )
        })
    catch 
        model[error] = Couldn't contact the bank, please retry later
else
    model[steps] = CP.getStepsModel


// ----------
View::displayModel(model)
```


The CheckoutProcess has various methods.
The accessors for the checkout data, like:
 
- setShippingAddressId 
- getShippingAddressId 
- hasShippingAddressId 
- setBillingAddressId 
- setPaymentMethod 
- setCarrier 
- set 


Note that the philosophy is to not check the values (trust the user) until the order is placed (placeOrder),
because otherwise we would have to much checking, plus we would still have the checking of the placeOrder method.


Then we have the main methods:

- setContext
    The context will be passed to constraints (see later) 
- isComplete
    This is the main method that execute the steps in order and checks whether or not they are valid.
    If a step is not valid, it fails and returns a model that is used to collect the data from the user.
    This is also a key concept of the CheckoutProcess implementation.
    
- getStepsModel
    returns a model (array) containing all the steps basic information (whether the step is done, current, and/or the
    model in case it's the current "failing" step which need to be redisplayed)
- placeOrder
    actually does place the order (potentially interact with external bank apis...)
- setCheckoutProcessStep 


   




The ajax script idea
===========================

When used inside an ajax script, the idea is quite simple:

```txt
CheckoutProcess::create->setShippingAddressId ( 6 )

// intent?
model[steps] = CP.getStepsModel
```


Note that this will impact the Constraint




The CheckoutProcessStep idea
==============================

The CheckoutProcessStep has the following main methods:

- prepare
    will check whether or not the form is posted, and if the form is valid, will update the 
    given CheckoutProcess instance so that the upcoming isValid method will pass
- isValid
    check constraints based on the CheckoutProcess instance (things like if false === CheckoutProcess.getShippingAddressId)
    This is also a key concept of this new CheckoutProcess idea: we don't need a third party medium to collect the 
    checkout data (as we did before) 
- getModel
    if the step is not valid, this method will be called.
    It will return the model of the form/thing necessary to collect the relevant checkout data that will make the step valid




