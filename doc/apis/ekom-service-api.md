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










