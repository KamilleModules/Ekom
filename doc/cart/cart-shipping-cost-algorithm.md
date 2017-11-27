Cart shipping cost algorithm
==============================
2017-11-27



Schema:
[cart-shipping-cost-algorithm](https://github.com/KamilleModules/Ekom/tree/master/doc/ekom-schemas/cart-shipping-cost-algorithm.pdf)






This algorithm determines whether or not the shipping cost apply on the cart model.


The shipping cost applies if all the following conditions resolve to true:

- the cart weight is greater than 0
        Otherwise we assume the cart has no product at all,
        or no shippable product
- a carrier is available to handle the shipping
        The app can provide a default carrier,
        or the user can select a carrier during the checkout process (if there is 
        a carrier choice), in which case the user choice prevails.
        
        If there is no carrier available, this only means we need to wait until the user
        selects one during the checkout process.
        
- the carrier is able to evaluate the shipping cost
        A carrier has a getShippingInfo method, which receives the carrierContext (context) parameter.
        The context is an array containing all the values ekom think are potentially useful
        to evaluate the shipping cost; the context is currently this:
        
            - shippingAddress: array:addressModel|null  (@see EkomModels::addressModel())       
            - shopAddress: array:shopPhysicalAddress|null  (@see EkomModels::shopPhysicalAddress())         
            - cartWeight: number         
            - cartItems: array of items from the primitive (being in construction) cart model (see CartLayer)
            
            
        Otherwise, this means the carrier hasn't all the information it needs to evaluate the shipping cost correctly,
        and this means we need to wait for the user to complete some checkout process steps that would release
        this information and make it available to the carrier.                      
        
        
        
Note: with this algorithm, notice that the user being connected is not necessarily
a condition to having the shipping costs applied.

That's because although most carriers need the user shipping address (only
available if the user is connected) in order
to develop their heuristics, some other carriers might declare a fixed rate for instance,
and so in that case the user being connected will not influence the carrier's shipping cost.     



About shop address
-----------------------
Being nice with the client is our goal (ekom presumes), and so if your shop has multiple physical addresses (i.e.
it can ship an order from multiple locations), then we should always select the physical address closest to the shipping
address, in the hope of reducing the shipping cost for the customer.

In order to do so, when the user selects an address during the checkout process, we also recalculate the closest shop
address in the background (notice that the user can't choose the shop address, this is something that ekom does only).

In terms of implementation, since the only place to change the shipping address is via the CCD (CurrentCheckoutData)
setShippingAddressId method, that's where we hook our logic and store the shop address id (in the CCD too).



