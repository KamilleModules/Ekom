Things I discovered with prices
==================================
2017-08-24



What I discovered can help the beginner implementor with her journey of creating
an e-commerce module.

Those are basic things that you may already be aware of, but for me they were all discoveries.





Tax ratio
======= 

So in an e-commerce, you have the problem of taxes.
Some products have one or more taxes applied to them.


I discovered that no matter how many taxes apply to them,
you can always go from the original number to the final result by multiplying by one number, which I call the taxRatio.


So, if your product has only one tax applied to it (for instance 20%), then this number is quite easy to find: 0.2.
Now, if you have multiple taxes applied to a product, there are two ways to apply the tax:

- apply the sum of the taxes 
- apply the taxes one after the other


So for instance, if we have a product with tax 20% and tax 30% applied to it.
 
In the first case (apply the sum of the tax), it's equivalent to have only one tax of 50% applied, so the taxRatio
is: 0.5 (no big deal).

In the second case (apply the taxes one after the other), I had to made the following tests:


- original number: 50
- +20%:   50 x 50*.2 = 60
- +30%:   60 + 60*.3 = 78

taxRatio = 78/50 = 1.56

- original number: 1200
- +20%:   1200 x 1200*.2 = 1440
- +30%:   1440 + 1440*.3 = 1872

taxRatio = 1872/1200 = 1.56


- original number: 7
- +20%:   7 x 7*.2 = 8.4
- +30%:   8.4 + 8.4*.3 = 10.92

taxRatio = 10.92/7 = 1.56


So, as we can see, the taxRatio is the same no matter which number we apply it to.

In other words, finding the "with tax" version of a number is just a matter of multiplying it by the taxRatio.







Applying discounts on price with tax
==============================

Then I was struggling with this question:

Given that your e-commerce can switch from b2b (prices are without tax) to b2c (prices are with tax),

when you show the price with tax, if the price has a discount, how do you find the price with tax number?


- do you take the original price with tax and apply the discount on it?
- or do you take the discounted price without tax and apply the tax on it?
- or is it the same?


To be honest I don't know yet, so let's write some tests down:



Text context:
-----------------

The product's price for the first test is: 50 without tax.
The taxRatio is 0.2.

The product's price for the second test is: 1200 without tax.
The taxRatio is 0.2.


For both tests, we will apply the two different types of discounts (that I know of):

- a fixed amount of 5
- a percent amount of 30%



First product test battery: trying the tax then discount approach
------------------

For the fixed amount discount of 5:

```txt
price without tax: 50
price with tax: 50 + 50*0.2 = 60
price with tax and discount: 60 - 5 = 55
```

For the percent amount discount of 30:

```txt
price without tax: 50
price with tax: 50 + 50*0.2 = 60
price with tax and discount: 60 - 60*0.3 = 42
```


First product test battery: trying the discount then tax approach
------------------

For the fixed amount discount of 5:

```txt
price without tax: 50
price without tax and discount: 50 - 5 = 45
price with tax and discount: 45 + 45*0.2 = 54
```

For the percent amount discount of 30:

```txt
price without tax: 50
price without tax and discount: 50 - 50*0.3 = 35
price with tax and discount: 35 + 35*0.2 = 42
```


As we can see the test with fixed amount discount fails,
but the test with percent amount discount seems to succeed.


So, the conclusion is that the algorithm that we choose to compute the price with tax and discount 
matters.


Shall we apply the tax on the original price or the discounted price?
=============================

So, following up on the previous section, my two cents on this is the following idea:

imagine that you want to show a product with its discounted price, and the original price with a line-through,
as an incentive for customers to buy the product.

The user could be b2b or b2c.

If you want to do this in b2b mode, this implies that you display the price with discount without tax.

So, maybe one should apply the tax at the original price level rather than on the discounted price?

It doesn't prevent you from using one algorithm or the other when it comes to computing the discounted price with tax,
but at least if you want to show the original price with tax, you can.



