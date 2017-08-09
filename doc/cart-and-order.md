Cart and order
====================
2017-08-09


I've been working on different things for a month, and now I've forgotten how the cart and order work.
Oops.
The code is procedural, and I have too many documentation files.
I shall simplify all this.





- Cart
    The cart is stored in the session.
    Cart is explained here: 
        class-modules/Ekom/doc/cart-and-order.md
        ```txt
        - ekom
        ----- cart:
        --------- $shopId:
        ------------- items:
        ----------------- 0:
        --------------------- quantity: 5
        --------------------- id: 650
        ----------------- 1:
        --------------------- quantity: 1
        --------------------- id: 12
        ----------------- ...
        ------------- coupons: array of valid coupon ids
        ```           
        
        Coupons are stored in the cart.        
        The cartLayer is the main api to the cart.
        
        There is a getCartModel method which returns a "cart model", suitable for templates displaying.
        The internal steps of collecting cart model are:
            - calculating line prices and total
            - adding/rechecking coupons (a coupon might become invalid after a page refresh)
            - adding carrier information
            - modules hooks
        
        
        
    
- Order
    The order is also stored in the session, and uses the session cart.
    
    The CheckoutLayer helps with the checkout process, it has the following methods:
        - getOrderModel: returns a suitable model for templates to display.
                The order model's conception is ekom-order-model-7,
                        https://postimg.org/image/xt2yqay1r/
                The order model is built on top of the "cart model".
                Details can be found in the database-$date.md document.
                
        - placeOrder: places the order
    
