Front form success
====================
2017-07-16


When you submit a form in the front, if the form is successful, you probably want to display
a success message.

But how do you display that message:

- do you use the same controller?
- do you use another controller?


My answer is that we use another controller, because we want to avoid the situation
where the user refreshes the success page multiple times, re-posting the form every time.


So, if that's something possible, I suggest you do it.


For the template, you can simply re-use the original template name, but with the "-success" suffix,
so for instance:

the

- my-login

template becomes

- my-login-success

