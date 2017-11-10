Ekom apis
==================
2017-11-10



Ekom provides methods using different apis:

- static api
- js api
- service api


The static api is located in the **Ekom/Api** directory.
It contains methods for the model (as in claws-mvc) to use.
This is the most powerful api in ekom.


The js and service apis provides the most essential "public" methods
to alter the ekom model (as in claws-mvc) from the outside. 

The js api theoretically allows the gui to alter the model (claws-mvc)
from js.
It's a js object with methods like addCartItem.


The js api uses the service api in the background.
The service api is an api that you can request using a simple post request.
It also let you alter the main objects in ekom (methods like addCartItem).










