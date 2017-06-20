Laws dynamic widget binder
=============================
2017-06-20


A controller helper.

Sometimes, controller want to allow modules to decide dynamically which widgets should be used.

This case happens to me right now (i.e. at the time of writing), my goal being the displaying of a product list
with attributes selector widgets on the left.

Depending on the category we are displaying the attributes will not be the same.
For instance, the "select color attribute" widget might be only displayed on pages which contains at least a product
with color attributes.

Now the first thing we want is to provide a widget depending on the available attributes.

The available attributes can be collected by the controller, but giving the modules the ability to attach
the widgets themselves offers us at least two benefits:

- first, the modules get to choose the form of the widget
- then, we can also add extra widgets if we wanted, for instance a "select manufacturer" widget, which is not
    an attribute, but could also be a listModifier (see listModifier document)