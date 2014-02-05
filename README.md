# Pallo: MVC Library

Model-View-Controller library of the PHP Pallo framework.

## Code Sample

Check this code sample to see some possibilities of this library:

    <?php
    
    use pallo\library\http\Header;
    use pallo\library\http\HttpFactory;
    use pallo\library\http\Response;
    use pallo\library\mvc\dispatcher\GenericDispatcher;
    use pallo\library\router\GenericRouter;
    use pallo\library\router\RouteContainer;
    use pallo\library\router\Route;
    
    // prepare some routes
    $route = new Route('/', 'testAction');
    $route->setIsDynamic(true);
    
    $routeContainer = new RouteContainer();
    $routeContainer->addRoute($route);
    
    // get the request and response
    $httpFactory = new HttpFactory();
    $httpFactory->setRequestClass('pallo\\library\\mvc\\Request');
    $httpFactory->setResponseClass('pallo\\library\\mvc\\Response');
    
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