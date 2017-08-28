Unique product id
======================
2017-07-07

upid

There are different types of products.
By default, there is only one type: the default type.

But then, modules can bring their own types to accommodate their needs.
They would do so when the default type is not enough anymore, it doesn't fulfill their requirements.


For instance, the training_product type, brought by the EkomTrainingProducts module,
allow us to create training products, which extend a regular product with new characteristics,
such as the date when the training event occurs, and the location where it occurs.


The date and location aren't regular ekom attributes (because the time goes on and on,
and if the date was an attribute, we would have staled attributes and we would have to create
new attributes for every event).

So the date is not an attribute, but it still characterizes the product: we make a difference
between the same product sold on July 9th and a product sold on July 20th.

In ekom, those products have the same product id, so the product id is not enough anymore
to identify a product instance and all its characteristics.

Also, maybe in the future, other types of characteristics, other than time, will appear and
the problem might come back again.

The unique product id is the ekom's default way of tackling this problem.

When a product is added in the cart, the cart uses the upid (unique product id),
and not the pid (product id).

However, the pid can be guessed from the upid, for ekom developers' convenience.


Here is the upid notation:

- upid: <pid> <trail>?
- trail: -complementaryId
- pid: int
- complementaryId: int


The trail is only necessary if the product is not of type default.









