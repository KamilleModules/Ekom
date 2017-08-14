Order Builder
================
2017-08-13



The OrderBuilder is a helper to implement a certain type of 
one page checkout order in ekom.


There is a specific logic to understand before you can use it,
and hence this documentation.



The actors
=============

There are two main actors:

- the orderBuilder 
- the steps


Steps are attached to the orderBuilder.
The order of the steps matters. 

Each step can have one of the following states:

- irrelevant: the step doesn't apply in the given context
- inactive: the user will have to do/review this step later
- active: the user is currently interacting with this step
- done: the user has already interacted with this step



The orderBuilder is responsible for keeping track of the states
of every step.
It passes the summary of steps to the View, which then displays
each step with the appropriate visual cue.

Internally, the OrderBuilder actually proceed in two phases:

- ask the steps if they are relevant to this context, and if not discard them
- execute the process method of the first step which state is not done yet
            (we will explain the process method below).
            

In other words, and that's why order matters: to the eyes
of the OrderBuilder, the step to be executed is:

- THE FIRST STEP WHICH STATE IS NOT DONE (discarded steps excluded)

That's how the orderBuilder knows which step is the current one.            
        



The process method
=====================

The process method is part of the Step class.

The process method is the trickiest part of this system, because
it serves two purposes:

- check that the step has just been done NOW
- returns the model of the step (for the View)


To help understand why there are two steps, please visualize
that a step is nothing but a form.

The form can be posted.
When the form of a step is successfully posted, the step changes
from the active state to the done state.

But since only the Step knows whether the form was successful or not,
this also means that ONLY the Step can detect the state change.

On the other hand, it's also the responsibility of a step to return
the model, since only a step knows what it is made of.



Therefore, the signature of the process method is like this:

- array process ( context, &justDone=false )

Two outcomes are possible:

- either the step's form hasn't been validated yet,
        in which case the process method returns the model array.
- or the step's form has just been posted successfully, in which
        case the process method's output is ignored,
        but the flag justDone should be set to true (so that
        the orderBuilder can register the new state of the Step)        
        
        

The context is given by the OrderBuilder, it can be anything,
depending on your concrete application.





Take your time to digest this document, and hopefully the logic
behind the OrderBuilder appears clear to you.





 










