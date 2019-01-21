Ekom order model
=====================
2017-11-09

[![ekom-order-model9.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model9.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-order-model9.jpg)



The ekom order model describes how the price is transformed from the original price (set by the shop owner)
down to the grandTotal which is the price the user will pay in the end.

It's used all across the ekom code spectrum.


Ekom order model is a set of steps.
Each step (except the first one) is the result of a transformation of the previous step.


- originalPrice: the price set by the shop owner
- (apply applicable taxes)
- basePrice: the price with applicable taxes applied to it.
            Note: we had to choose whether or not the tax would apply on the original price,
            or the discount price.
            This model chose to apply taxes on the original price.
            If you need otherwise, you need another model.
- (apply discounts, target=basePrice)            
- salePrice: the sale price (paid by the user for this particular product)
                Note that this price includes the taxes constraints,
                which means that it could be either a price with or without taxes.            
- (multiply by the purchased quantity)
- linePrice: the sale price for this item(s) in the cart        
- (sum all lines)            
- linesTotal: the total of all lines in the cart        
- (apply coupons, target=linesTotal)        
- cartTotal: the total of the cart        
- (add shipping costs)        
- cartTotalWithShipping:         
- (add coupons, target=cartTotalWithShipping)        
- grandTotal
            


Tip: in **ekom product box model** and **cart model**, all prices related to this **ekom order model** are prefixed with
the "price" prefix.





About multiple shipping addresses
====================================

The older versions of ekom order model suffered my try to incorporate a "multiple shipping addresses"
feature right off the bat.

Now with the new model, much simpler, new ideas emerge for implementing a "multiple shipping addresses" system.

In a nutshell, the idea of implementing parallel carts, much like the estimateCart, is something 
that I hope might inspire the implementor.

