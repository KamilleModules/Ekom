Ekom checkout StepManager guidelines
===========================
2017-10-26



This document's goal is to explain the general technical mechanism to use  
in the ekom checkout process.

Implementation is left to authors for now, as I'm in a rush.
Note: these are only my thoughts/recommendations, you can choose another path if you feel like it. 






Steps
-------------

We're using steps that we attach to a stepManager.
This allows us to have dynamic steps.

For instance, in my company, some steps are only attached if the cart contains a certain
type of product.




StepManager logic
---------------------

The stepManager follows this general algorithm:


- get the steps order 
    - this is so that we can easily test it and spot errors that could occur
- choose which step to display now
    - this is explained in greater details below
    
    
    
Step done
------------
Each step is a conceptual object that has a done state.
The done state is how the StepManager decides which step to display.



Displaying the right step
--------------------------

The stepManager follows this algorithm to find the step to display:

- find the first not done step

That's it.



Step's done state management
---------------------------
The step manager keeps track of the done states (we will see how in a moment).
It uses the php session as memory.
The stepManager's memory is cleaned up only when the order is completed (i.e. the user has
shown his/her intent to pay us).

This allows us to skip the steps that the user already completed a few moments ago.
In the event that some steps ask a lot of questions to the user, this move is a rather good optimization,
at least from the user's perspective. 
 
 
 
Step and StepManager communication, the listen method
--------------------------------

The Step provides a listen method, with the following signature:


- array:model     listen ( array &doneData=null, array $defaults=[] )


This is a one-does-all method (bad oop practice in general, but alleviates the StepManager
code, easing, maybe, the debugging):


- the model array returned is the one used by the view
- the defaults array is provided by the StepManager (if the stepManager has it in memory), so that the step form
            can be pre-filled with sensible values
- at the same time, the listen method process a form if any 
        It's responsible for knowing whether or not the relevant form was posted
- if a successful form is posted, then the data to save are passed via the doneData 
        referenced parameter




Step navigation
---------------
The user should be able to navigate the steps, 
but only those that she/he has completed already (those with the done state).








Nested steps implementation ideas
------------------------------------
If your step has nested steps, you can use the following idea:

register all your sub-steps as regular steps, and rely on the view to display the relevant gui/form.

This has the benefit of not messing up with the stepManager default logic (plus it's very easy
to handle those cases from the view, a couple of ifs might do the trick). 





Step defaults values
-----------------------

As we provide the user with the ability to navigate the steps, this means that the user can go back to a previous step.
When she/he does so, we also want to fill the step form (most steps are forms) with the data that the user already
completed.

In order to do so, the StepManager, when calling a step, passes the stepData saved in memory if any.



Step states
---------------
We've already talked about the done state, but what about the other states?

A step can be in one of the following three states:

- inactive
- done
- active


- Inactive is the default state, this is the state of a step which never reached the "done" state
- Done is the state reached when the user successfully completes the step's form.
        Once the done state is reached, it can never be lost (unless the order completes, in which case all done 
        states are cleaned).
        The user can focus a done state, which them temporarily becomes active.
        Once the user leaves the step, the step takes the "done" state again (it can not have the inactive state again).
        
        
- Active is the state of the step having the focus (the current step).
        There is always one step and one only having the active state.  








