Rendering templates with intent
=========================
2017-11-11



This is my opinions about how templates should be done.





The problem
====================

Let's first understand the initial problem.

We are displaying a product page.
The portion of the page that we are interested in in this discussion is called the product box.
The product box displays some essential information about the product, like:

- the title
- the ref
- the description
- the product attributes (color=red, color=blue, color=green)
- the price
- the quantity selector
- the "add to cart button"


When the user changes an attribute, we expect the product box to update with the new data:

- new reference
- maybe new price
- the selected attribute has changed
- ...
 
 
So, how do we do that?

My first idea was to make an ajax call that would return the new data as a json array, and then inject
this data back in the template.

The other option I thought of was rendering the product box server side, and inject the computed html
directly in the dom, replacing the old one. 
 
Both solutions were acceptable, but I was worried about my mvc structure, and thought that the first solution
would be less painful, since I didn't need to think about who is responsible for rendering the html server side 
(in a modular architecture): I could just ask the model for the data. 
 

So, but my decision led me to this situation where now I've to parse the json data and inject it in the template.
What's funny (well, not so funny) is that to make a proper injection, I found myself recreating the html template
with js.
For bad reasons, I ignored this warning, thinking that I could live with that, and continued the application 
development.
Today, I've made all templates based on this system.
Fortunately, not all of them are dynamic.

But since today I'm revisiting the whole website in search for optimizations, striving for simplicity,
this system is no longer acceptable, it's just too painful.


I've looked into react's quickstart tutorial, and it turns out I totally dig the semantic approach of react,
it seems to me that react provides an elegant approach to this precise problem, and more.

However, being pragmatic and short on time, I also noticed that in 2017 react suffers a SEO problem, or should I
say the search engines are not ready for react yet.
There are some workarounds, but as I said I'm short on time so I don't want to risk to loose our SEO, which
in my company is a crucial value.

If I was developing my own apps, this would have be totally different...
but I'm not.
So, back to old school methods.

What's left?

The second solution: use the same simple php function to render the product box twice.
This sounds like heaven, the static html is displayed with the page, and then when the user changes
an attribute, we re-use the same function and inject the html back in the page.


Before implementing, war plan, final thoughts
------------------------

Ok, but let's go deeper.
The mvc model must not be broken.

Ekom uses ecp services, which return an array.

When the user adds an item to a cart, she/he calls the cart.addItem method,
which basically interacts with the ekom model to add the item to the cart.

What we can do is add a hook in this method, and modules can decorate the returned array.

In particular, the ThisApp module (or any other module that you want) can add an **html** property to the array,
which contains the pre-rendered html, re-using the php renderer that we used for displaying the 
front template in the first place (re-usability).

Then, the template being under the authority of ThisApp (in this example) can legally 
be aware of the presence of this html property and inject it in the page.

Done.
The rest is implementation details in the template (how to inject the html block, can we factorize 
the injection method?, ...).


Actually, I missed one piece of the puzzle: the intent.

The caller js code should pass the intent to the ekomJsApi.

The intent expresses with an arbitrary string what we try to render (a sidebar?, a cartItem?, a list of items?, ...).

The ekomJsApi transmits the intent to the service, and finally the modules hooks catch it and NOW know what renderer
they should use.

Also, on a page, multiple widgets might work together.
For instance, if you update the quantity of an item in your cart, and maybe you're on the checkout page, then
the prices on the checkout widget should update as well.

It is therefore recommended that another js tool collects all the intents on the page before transmitting
the payload to the target service.

See this figure:

[![rendering-templates-with-intent-markers.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/rendering-templates-with-intent-markers.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/rendering-templates-with-intent-markers.jpg)



Note: this means that the ekomJsApi always allows for an intent option where appropriate.

 
Note2: you can embed the js in the rendered html if you want (be cautious of what you are doing though), 
or (if you use jquery delegate events a lot, like me) you can just inject pure html in your template,
and keep your js code handling the injected portion.
Depends on your js coding preferences, you can even mix both techniques.









 



