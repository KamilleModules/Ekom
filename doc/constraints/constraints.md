Constraints to keep in mind while creating the ekom module
===============================
2017-11-05




Discount constraints
============================
From database/discounts.md.

When you create a discount,
In case of **fixed**, the operand represents the discount in the shop's currency.

So, when you change the shop's currency, you might want to adapt the discounts
as well (unless you reason with abstract numbers which could be a mode to create if that's what 
you are going for...).