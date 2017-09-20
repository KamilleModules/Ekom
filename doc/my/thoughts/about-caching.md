About caching
================
2017-09-20


Recently I thought about caching, wondering what was the fastest technique to render a page...



So, what's the fastest technique to render a page?

Display its html content.

Ok. Fine.
Let's now consider two common problems:

- the user is connected
- the user's cart



When the user is connected
=============================
When the user is connected, on most pages we have the same content, except that the top bar indicates that the user
is connected.


The php variation
--------------------- 

Ok, how do we handle that.
A logical approach is to state that the page is not cachable anymore and so we trigger the normal page rendering.

By normal page rendering, I mean the uri is parsed, it goes into the application router, controller, and the view 
is rendered.
That's a lot of processing, so how can we avoid that?

A more performance aggressive approach is to use a str_replace injection mechanism.
For instance, imagine in your top bar, you put this code:

```html
<div class="topbar">
    <!-- hi:connection module -->
    <a href="/some/uri">Please connect</a>
    <!-- hi:end connection module -->
</div>
```

In the above code, we have wrapped the variable part of the top bar into html comments; hi stands for 
html injection (I just made that up), it helps finding those kind of tags.

Now what?

In terms of implementation, we could just check the session: is the user connected?
If so, then replace the "hi tag" by content A, else by content B.

So, basically we would do an if statement, a call to a (in this case very simple) renderer,
and a preg_replace.

I've not tested it yet, but I bet this is much faster than going through the usual routine of the 
application (router, controller, view...).


The js variation
-------------------

What if we use js?

First prepare the top bar as if the user were not connected, but prepare to react to a js event. 
```html

<div class="topbar" id="hi-topbar">
    <a href="/some/uri">Please connect</a>
</div>
<script>
    $(document).ready(function () {
        ekomApi.inst().on("userReady", function(userInfo){
            var jTopBar = $('#hi-topbar');
            jTopBar.empty();
            var s = '<a href="'+ userInfo.accountLink +'">';
            s += 'Welcome ' + userInfo.pseudo;
            s += '</a>';
            jTopBar.append(s);
        });    
    });
</script>


```

And at the end of the page, just before the closing html tag, we would add the js code that triggers the userReady event.
This approach is interesting as we can add more than one subscriber, BUT the userInfo variable needs to 
contain all the variables that all subscribers (here we have only one) require.

