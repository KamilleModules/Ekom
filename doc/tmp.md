Constraints of ekom product box model
========================================


- If the module uses "product details system", it must provide the productDetails key
- properties starting with _ are reserved by ekom





Constraints of ekom cart
========================================
- add to cart/update the cart:
        first ask the question: is it legal/possible? (i.e. do we allow negativeStock, and if no,
            do we have enough stock, and if no an exception is thrown)

- if addItem method is called and the product contains non empty minor details, don't increment the quantity
    (because this means the configuration has been changed, this is an "user updating the product" mode)