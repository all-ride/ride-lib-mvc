# Ride: MVC Library

Model-View-Controller library of the PHP Ride framework.

It builds on top of the HTTP and routing library to handle input and output.

## What's In This Library 

### Request

The MVC _Request_ is an extended HTTP request.
The route is integrated which gives you the possibility to get extra properties from it like the base URL and incoming arguments.

### Response

The MVC _Response_ is an extended HTTP response.
It adds methods to deal with views and messages.

### Controller

A _Controller_ handles the incoming request and translates it into a response.
The workhorse of a controller is an action.
Multiple actions can be defined in one controller.
Each action passes the input to the model to perform the necessairy logic.
The result of this action is set to the response, possibly through a view.

To translate incoming request into actions, the actions must be defined in the routing table.

### Model

A model contains the logic of your domain.
There is no interface for this since it can by anything you want or need.
It's completly up to you.

### View

A _View_ is a representation of the result.
It's a data container of variables which will be rendered when sending the response.
Different views for the same action can easily implemented like HTML, JSON, XML, ....

### Message

The _Message_ is a data container for a single message.
You can add multiple messages to a response.
Usefull to add warnings or error and success messages when submitting a form.

## Code Sample

Check this code sample to see some possibilities of this library:

```php
<?php

use ride\library\http\Header;
use ride\library\http\HttpFactory;
use ride\library\http\Response;
use ride\library\mvc\dispatcher\GenericDispatcher;
use ride\library\mvc\message\Message;
use ride\library\router\GenericRouter;
use ride\library\router\RouteContainer;
use ride\library\router\Route;

// prepare some routes
$route = new Route('/', 'testAction');
$route->setIsDynamic(true);

$routeContainer = new RouteContainer();
$routeContainer->setRoute($route);

// get the request and response
$httpFactory = new HttpFactory();
$httpFactory->setRequestClass('ride\\library\\mvc\\Request');
$httpFactory->setResponseClass('ride\\library\\mvc\\Response');

$request = $httpFactory->createRequestFromServer();
$response = $httpFactory->createResponse();

// route the request
$router = new GenericRouter($routeContainer);
$routerResult = $router->route($request->getMethod(), $request->getBasePath(), $request->getBaseUrl());

// dispatch the route
$returnValue = null;

if ($routerResult->isEmpty()) {
    $response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
} else {
    $route = $routerResult->getRoute();
    if ($route) {
        $request->setRoute($route);

        $dispatcher = new GenericDispatcher();
        $dispatcher->dispatch($request, $response);
    } else {
        $allowedMethods = $routerResult->getAllowedMethods();

        $response->setStatusCode(Response::STATUS_CODE_METHOD_NOT_ALLOWED);
        $response->addHeader(Header::HEADER_ALLOW, implode(', ', $allowedMethods));
    }
}

// send the response
$response->send($request);

// the test action
function testAction() {
    global $response;

    $response->addMessage(new Message('This is a test action', Message::TYPE_WARNING));
    $response->setBody('test: ' . var_export(func_get_args(), true));
}
```

### Implementations

For more examples, you can check the following implementation of this library:
- [ride/web](https://github.com/all-ride/ride-web)

## Installation

You can use [Composer](http://getcomposer.org) to install this library.

```
composer require ride/lib-mvc
```
