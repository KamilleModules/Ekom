Ekom service api
==================
2017-11-10


Ekom service api provides methods via http calls (i.e. $_POST and/or $_GET).

It's composed of two apis:

- ecp: (ekom communication protocol), return some json data
- html: displays some html to the screen




General organization 
==================
Both apis (ecp and html) share a common base organization.


Ecp api is a script that listens to an action identifier.
When this "action identifier" is passed, the service is executed,
and the output is displayed.

The action identifier is a string passed as a $_GET parameter (using the action parameter).
It's composed of two elements:

- action identifier: <serviceNamespace> <.> <serviceName>
- serviceNamespace: this should represent the object you are going to alter,
                    or the object of the api you want to interact with.
- serviceName: the name of the service you want to execute.
                
 
Other params are passed via $_POST.                    
Html api works the same.





ECP
========

Ecp codifies the different outcomes of a script.
When an ecp service is called, it always return a json payload containing the following:

- $$error$$: if this key exists, this is an error message intended for the 
            public user (i.e. the customer).
            It's assumed that a js layer displays this error message to the user.
            
            The idea is that if a dev error occurs server side, a public generic error message
            is shown to the user, while the error is logged server side.
            So that the devs can work on the errors, while the public user is not bothered with/aware of
            the technical details of a dev error.
            That explains why there is no $$devError$$ level.
             
            
- $$invalid$$: if this key exists, this means that the parameters passed to the service
                aren't sufficient to execute the service correctly.
                
                The js layer shall console.log the error message, that's a developer
                error which should be fixed asap.
                
                Server side, this type of error is not logged by default,
                but a hook allows you to do so with your modules. 
- ...all other properties expected by the caller. 
        Those are returned when the service was successfully executed.                 



Note: in the background the ecp services should use the following exceptions
when appropriate:

- EkomUserMessageException
- EkomInvalidArgumentException


Html
========

This api just displays some html.
There is no codified error handling as for now.










Create your own ECP service, preserve the harmony
===============================

See in ecp/api.php how it's done.
You don't want to disrupt the existing harmony, because this harmony is what makes the api SIMPLE.
You see, there is a flow between the js api and the service api, they use the same method signatures
for the most part, which makes it easy for the developer to use one of them, or even both of them.

Now inside your ecp service, it's important to understand the interaction with the js layer too:
here is what you should be aware of:

- throwing an EkomInvalidArgumentException will console.log the error.
            You might want to log it server side.
        
- throwing an EkomUserMessageException will display a message to the user (by default using the alert function,
        but you can enhance this method if you want).
        
- throwing any other exception will display a generic error message to the user (An unexpected error, it has
        been logged and we're currently working on it!), while it's logged server side for your team to work on it.
        It's important to understand that the ekom js methods want an user feedback when it goes wrong,
        because the user is at the origin of the call in 95% of the cases.
        And for the last 5%, it doesn't hurt to see the generic error message if something goes wrong anyway.
        That's why we believe this system is good enough for ekom.
        So remember, throw the right exception, and harmony will be preserved.






