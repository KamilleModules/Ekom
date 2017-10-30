Ekom Checkout synopsis
======================
2017-10-28


This document completes and supersedes the ekom-checkout-synopsis-2017-10-28.md document.




When the user arrives on the checkout page.


The objects involved are:


- CheckoutPageUtil
    it stores the user preferences in memory (billing address, shipping address, payment method, ...),
    so that if the user refreshes the page, the checkout step she/he did  doesn't vanish.
    



CheckoutPageUtil
==========

The ekom checkout process works with steps.

Note: the **ekom-checkout-stepmanager-guidelines.md** document explains
the inner mechanisms of the CheckoutPageUtil object.


The default steps are:

- login
- shipping
- payment


Modules can add their own steps using the **Ekom_CheckoutPageUtil_registerSteps** hook.
See **CheckoutPageUtil** source code for more info.


Each step is assigned a position, so that modules can work well together.
The default positions are:

- login: 1000
- shipping: 2000
- payment: 3000


Note: steps are executed in ascending order


When a step is completed, a data is saved in the memory.
Memory access is done via the following methods:



```php
CheckoutPageUtil::cleanSessionVars()
CheckoutPageUtil::getSessionVars()
CheckoutPageUtil::getStepsData()
CheckoutPageUtil::getStepData($stepName)
// maybe more to come...
```

The memory structure is returned by the getSessionVars method,
and looks like this:


```txt
- done: 
    - $stepName => bool:isDone
- steps: 
    - $completedStepName => mixed:stepDoneData
```

Note: the getModel method has to be called at least once before
the "done" entries are populated (otherwise it's an empty array). 


The getStepsData method returns only the steps entry of the sessionVars
(i.e. it's a shortcut, and the recommended method).

Similarly, the getStepData method returns only the step data for the given step, or false
if the data is not yet ready.



