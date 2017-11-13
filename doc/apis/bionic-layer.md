Bionic layer
==================
2017-11-10



The bionic layer is a js tool that connects your template to the ekom js api automatically.

It's called bionic because it's like an extension/enhancement of the html template author capabilities. 




The idea
=================

There is one question: how do you add a product in your cart?

Every template author is different.
And so if we don't do anything about it, every template author will have its own opinion about it.

While diversity is good sometimes, in ekom we like the idea that those low-level tasks should be no-brainers,
we want to focus on other more important things, like our app-specific things.

That's why we provided the service api in the first place.
Oh, so now template authors know that if they call this service, they can add a product in the user cart.

Great news. But we weren't satisfied yet.
That's why we provided the ekom js api.
Oh, so now template authors know that if they import the ekomApi.js file in their page and call this method, 
they can add a product in the user cart.

Great news, because now they don't need to know about the service layer. But we weren't satisfied yet.
That's why we provided the bionic layer.
Oh, so now template authors know that if they import the ekomApi.js file and the bionic.js file in their page, and 
add certain markup to certain element, they can add a product in the user cart.


Great news, because now they can add a product to the cart without knowing the javascript layer.
And now we're satisfied :), because every template author knows html.




How does it work?
======================

We distinguish the following types of interactions:

- click
- change
- change & keyup

The click is when you click on a button or an element, and an action occur.
The change is for selectors; when you select an option, an action occur.
The "change & keyup" interaction is used for input of type number.
The number inside the input can be changed either by typing directly the number with the keyboard (like a regular input),
or by clicking the up and down arrows on the side of the input (provided natively by the browser for this kind of input),
or even by pressing the up and down arrows on your keyboard (the behaviour is also provided by default by the browser).


Each type of interaction has its own markup.
Some principles apply on both types of interaction, so we will be able to re-use our knowledge from an interaction 
type the other.

But for now, let's focus on the base principles.


Base principles
====================

Hello world
----------------

The minimal markup for a click interaction looks like this:

```html
<div class="bionic-btn"
        data-action="cart.addItem"
        data-param-quantity="1"
        data-param-product_id="6"
>This is my button</div>
```

The bionic layer automatically intercepts clicks on elements with class **bionic-btn** (and or children).
Once the click is intercepted, bionic process the element.

This click will call an ecp service (see **doc/apis/ekom-service-api.md** for more details),
and so we need to specify the action and some params.

This is done with the "data-*" attributes; in our example we yield the following:

- action: cart.addItem
- params:
    - quantity: 1
    - product_id: 6
    
Those variables will be passed to the cart.addItem method of the ekom js api automatically.


Targets
----------------

In our previous example, our quantity number was static.
However in real life, let's say that the user uses another input to set the quantity she/he desires.

We can make our **data-param-quantity** fetch its value from another element that we call target.

Here is how it's done.

```html
<div class="bionic-context">
    <div class="bionic-btn"
            data-action="cart.addItem"
            data-param-quantity="$quantity"
            data-param-product_id="6"
    >This is my button</div>
    <input type="number" class="bionic-target" data-id="quantity" value="2">
</div>
```      

What we have done is this:

- put a context around our elements,
    that's important because we will ask our quantity to be fetched from ANOTHER element,
    and if we don't constrain ourselves within a context, then there might be name collisions (i.e. we could fetch
    the quantity value from another element).
    A good rule of thumb is: always wrap your bionic sections in contexts. 
- then we've created a target.
    A target is an element with class **bionic-target**.
    The identifier of this target is given by it's attribute: **data-id**.
- finally we've set **data-param-quantity="$quantity"**.
        The part after the equal ($quantity) is called the "target string".
        When the value of a "bionic" attribute is prefixed with one dollar symbol, then this means:
        go fetch the value of the target element (of this context) which identifier is the value given after the dollar symbol.
        In other words, we've just ask to retrieve the value from the "quantity" target.
        
        
        
### Fetch value algorithm        
How is the value retrieved?
Bionic uses the following algo called "fetch value algorithm" (simple but not foolproof, does not cover all the cases):

- if the target has a name attribute ending with the double square brackets (indicating that this is an array),
    then bionic creates returns an array by collecting all elements with the same name (inside the given context)                
- else if jquery's val method is applicable, bionic returns the result of the val method
- else if jquery's val method is not applicable, bionic returns the text of the element                                        
   
             
If the "target string" is the expression "$this", then the target is the bionic object hosting the params.
     
Note: as a consequence of the "fetch value algorithm", the value might be either a scalar value, or an array (in case 
of checkboxes with the same name and ending with square brackets).


Here is an example fetching an array:
```html
<div class="bionic-context">
    <div class="bionic-btn"
            data-action="cart.addItem"
            data-param-quantity="$quantity"
            data-param-product_id="6"
            data-param-courses="$courses"
    >This is my button</div>
    <input type="number" class="bionic-target" data-id="quantity" value="2">
    
    <input type="checkbox" name="courses[]" value="1" class="bionic-target" data-id="courses">
    <input type="checkbox" name="courses[]" value="2" class="bionic-target" data-id="courses">
</div>
```      

Then the yielded data will look like this:

- action: cart.addItem
- params:
    - quantity: 2     // or another number
    - product_id: 6
    - courses: 
        - 0: 1          // assuming this checkbox was selected
        - 1: 2          // assuming this checkbox was selected
    

 
Creating arrays in the structure
---------------------


What if we wanted to create the following data instead?

- action: cart.addItem
- params:
    - quantity: 2     // or another number
    - product_id: 6
    - details: 
        - courses: 
            - 0: 1          // assuming this checkbox was selected
            - 1: 2          // assuming this checkbox was selected


There is a solution, change the name of the param that yields the courses values, like this:


```html
<div class="bionic-context">
    <div class="bionic-btn"
            data-action="cart.addItem"
            data-param-quantity="$quantity"
            data-param-product_id="6"
            data-param-details-courses="$courses"
    >This is my button</div>
    <input type="number" class="bionic-target" data-id="quantity" value="2">
    
    <input type="checkbox" name="courses[]" value="1" class="bionic-target" data-id="courses">
    <input type="checkbox" name="courses[]" value="2" class="bionic-target" data-id="courses">
</div>
```      

The only thing that changed, compared to our previous example, is the name of the param:

- data-param-details-courses

Notice the dash between details and courses.

The dashes in **data-param-** don't matter because bionic filters them automatically.

This dash between details and courses makes details an array, and courses an entry of that array.
Since we asked for an array (double dollar symbol), the value of courses will also be an array.



Now that we know the basic principles, we can look at the interactions that bionic provides.


Interactions
====================

As we said earlier, bionic understands the following interactions:

- click
- change
- change & keyup


By default, bionic assumes that all of those interactions will trigger an ecp service call using the **ekom js api**.

I say that because in the future, if bionic does something else, we could just add some **data-type** parameter,
which today implicitly defaults to **ecp** (for instance). 

An interaction of type ecp needs the following parameters:

- action: the action identifier, as specified in the **/doc/apis/ekom-service-api.md** document (use data-action attribute)
- ...different params: the params depending on the js api's method you want to call (use the data-param-* attributes)


The interactions are named after their triggering events.


Click interaction
------------------
The click interaction is the one that we used all along in the previous examples.
It's basically any element with the class **bionic-btn** on it.


Change interaction
------------------
The change interaction is any element with the class **bionic-select** on it.



Change & keyup interaction
------------------
The "change & keyup" interaction is any element with the class **bionic-number** on it.

On this type of element, the triggered event (change and/or keyup) might be fired multiple times
very rapidly, which might sometimes be unnecessary/undesirable.

To help with this, bionic only executes the function after the user has stopped typing for 250ms (by default).
To change the delay, you can add the following attribute to your bionic element:

```html
data-debounce-time="1500"
```



 

Extra attributes
===================

Bionic uses some other features as well:


NinShadow
--------------

Bionic can work with the concept of [ninshadow](https://github.com/lingtalfi/NinShadow), using the "data-ninshadow"
attribute to specify the target representing the "localized loader" to toggle (see ninshadow document)

```html
<div class="bionic-context">
    <input class="quantity-input bionic-number" type="number"
           data-ninshadow="ninshadow">
    <div class="nin-shadow-loader bionic-target" data-id="ninshadow"></div>
</div>

```

Note that we don't use the dollar prefix in the **data-ninshadow** attribute.
That's because this attribute has a special handling.



Intent
--------------

Intent is a concept implemented in ekom.
See **/doc/templates/rendering-templates-with-intent.md for more info**.

We can specify the intent using the **data-intent** attribute on a bionic object.

```html
<div class="bionic-btn" data-intent="mysidebar"></div>
```








