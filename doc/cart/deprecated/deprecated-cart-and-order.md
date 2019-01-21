Cart and order
====================
2017-08-09


I've been working on different things for a month, and now I've forgotten how the cart and order work.
Oops.
The code is procedural, and I have too many documentation files.
I shall simplify all this.






Here is a sample session data:

```php
array(2) {
  ["ekom"] => array(8) {
    ["front"] => array(2) {
      ["lang_id"] => string(1) "1"
      ["currency_id"] => string(1) "1"
    }
    ["cart"] => array(1) {
      [1] => array(2) {
        ["items"] => array(2) {
          [0] => array(3) {
            ["quantity"] => string(1) "1"
            ["id"] => string(2) "58"
            ["extraArgs"] => array(3) {
              ["qty"] => string(1) "1"
              ["product_id"] => string(2) "58"
              ["complementaryId"] => string(1) "0"
            }
          }
          [1] => array(3) {
            ["quantity"] => string(1) "1"
            ["id"] => string(4) "1934"
            ["extraArgs"] => array(3) {
              ["qty"] => string(1) "1"
              ["product_id"] => string(4) "1934"
              ["complementaryId"] => string(1) "0"
            }
          }
        }
        ["coupons"] => array(0) {
        }
      }
    }
    ["estimateCart"] => array(1) {
      [1] => array(2) {
        ["items"] => array(1) {
          [0] => array(3) {
            ["quantity"] => int(2)
            ["id"] => string(2) "58"
            ["extraArgs"] => array(3) {
              ["qty"] => string(1) "1"
              ["product_id"] => string(2) "58"
              ["complementaryId"] => string(1) "0"
            }
          }
        }
        ["coupons"] => array(1) {
          [0] => int(1)
        }
      }
    }
    ["EkomUserProductHistory"] => array(0) {
    }
    ["order.singleAddress"] => array(6) {
      ["billing_address_id"] => string(1) "6"
      ["shipping_address_id"] => string(1) "4"
      ["carrier_id"] => int(1)
      ["payment_method_id"] => NULL
      ["payment_method_options"] => NULL
      ["current_step"] => int(0)
    }
    ["ThisApp_trainingForm_currentValues"] => array(1) {
      ["you_are"] => string(3) "pro"
    }
    ["order-builder-1"] => array(2) {
      ["states"] => array(4) {
        ["login"] => string(4) "done"
        ["training"] => string(4) "done"
        ["shipping"] => string(6) "active"
        ["payment"] => string(8) "inactive"
      }
      ["data"] => array(4) {
        ["login"] => array(0) {
        }
        ["training"] => array(0) {
        }
        ["shipping"] => array(0) {
        }
        ["payment"] => array(0) {
        }
      }
    }
    ["referer"] => string(30) "https://lee/customer/dashboard"
  }
  ["frontUser"] => array(3) {
    ["id"] => string(1) "1"
    ["user_connexion_time"] => int(1502721135)
    ["timeout"] => int(3000)
  }
}

```



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
        But the trick is that only the "beforeShipping" coupons are used to compute the cart total.      
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
                The order model is built on top of the "cart model".
                Details can be found in the database-$date.md document.
                
        - placeOrder: places the order
    

    ["order.singleAddress"] => array(6) {
      ["billing_address_id"] => string(1) "6"
      ["shipping_address_id"] => string(1) "4"
      ["carrier_id"] => int(1)
      ["payment_method_id"] => NULL
      ["payment_method_options"] => NULL
      ["current_step"] => int(0)
    }


