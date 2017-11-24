Checkout process
=====================
2017-11-24

Work in progress




Overview
=============

The checkout process is a helper for displaying the checkout tunnel.



There are two main ideas:

- the singleton CheckoutProcess
- the CheckoutProcess flow


the singleton CheckoutProcess
---------------------------
How many checkout processes we can do in parallel in an e-commerce?

In ekom, just one.

The main idea is that the CheckoutProcess acts like a Registry (singleton): it's accessible at any time by any object.
Plus, it's stored in session.

Because of those properties, the CheckoutProcess can be updated from a static page and/or from ajax scripts,
which makes it quite flexible to work with.

This schema illustrates this idea of an always available CheckoutProcess:

[![ekom-checkout-process.jpg](https://s19.postimg.org/hcps8ik8j/ekom-checkout-process.jpg)](https://postimg.org/image/eimmv2i27/)



the CheckoutProcess flow
---------------------------

I learned it the hard way, but the flow is VERY important.

There are some rules that governs how the steps can be navigated and handled by the CheckoutProcess.

Those rules originates from practical concerns for the end users.

Knowing those rules is fundamental for conceiving this flow.

I believe the best way to understand those rules is to give the most complex but practical example I can think of,
and explain the rules from that example, since the rules come from that example in the first place.



So, this example is actually a real life example that I had to deal with in the company I'm currently working in.

Basically, we have the following steps:

- login 
- shipping 
- training1 
- training2 
- training3 
- payment 


Visually, training1, training2 and training3 are displayed in the same space.


The rules are the following:

- by default: we display either the lastVisitedStep if there is one, or the very first step otherwise
                The lastVisitedStep is actually useful to prevent inconsistencies if you REFRESH the page (don't forget
                that this is possible)
- also, the user can click on a step (and go to this step), but only if this step was already reached before.
        This promotes the idea that there is an order to the steps, and that order should be respected
- when a step is posted successfully, we immediately display the very next step in the order (and not the next "non valid" step).
        This rule is actually important for the user, especially in regard with the substeps (training1, 
            training2 and training3), because an user completing substep1 expects substep2 to be coming next, 
            even though it might have already been completed (I believe).
                







