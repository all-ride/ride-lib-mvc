# Ride: MVC Library

Model-View-Controller library of the PHP Ride framework.

## Code Sample

Check this code sample to see some possibilities of this library:

```php
<?php

use ride\library\http\Header;
use ride\library\http\HttpFactory;
use ride\library\http\Response;
use ride\library\mvc\dispatcher\GenericDispatcher;
use ride\library\router\GenericRouter;
use ride\library\router\RouteContainer;
use ride\library\router\Route;

// prepare some routes
$route = new Route('/', 'testAction');
$route->setIsDynamic(true);

$routeContainer = new RouteContainer();
$routeContainer->addRoute($route);

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

    $response->setBody('test: ' . var_export(func_get_args(), true));
}
```
