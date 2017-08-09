ListModifiers
==================
2017-06-20



List modifiers are used to shape a list of products.

A list of products can be modified using list modifiers.

There are two main types of modification:

- searching
- sorting

For instance, we can decide to search only for products which have a color attribute with a value of green.
Or we can order the list by ascending prices (for instance).

List modifiers comes in the form of an array of key/value pairs organized by modification type, for instance:


- search
    - color: green
    - size: m
- sort
    - price: asc



When used by ekom internal objects such as the ProductCardLayer, list modifiers are translated to **sql fragments**.


Sql fragments are of two types:

- join: affect the join clause
- where: affect the where clause



Modules can hook into this system and bring their own list modifiers.

For instance, if a module adds a manufacturer table and you want to be able to filter a list by manufacturer,
then you can hook into the ekom list modifier system (not yet implemented at the time of writing) and
implement your module logic to allow that feature.



Implementation notes
----------------------

ListModifiers are used to list the list modifiers of the current page, so that we can order them or do other 
manipulations on them.


They are guessed from the uri, and in Ekom we simply use $_GET.

But now when we have $_GET params we need to know whether or not that param is actually a list modifier or something else.

Therefore, we use a **collect list modifier** phase for that purpose.


S - M - L


Then, later we need to display links, which, when clicked, change the state of the list (via list modifiers), be it 
the order, the filters, or a combination of both.


List modifiers are not used on every page, but only on pages which need to display a list (obviously).





