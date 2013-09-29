<?php

namespace pallo\library\mvc\dispatcher;

use pallo\library\mvc\controller\Controller;
use pallo\library\mvc\exception\MvcException;
use pallo\library\mvc\Request;
use pallo\library\mvc\Response;
use pallo\library\reflection\Callback;
use pallo\library\reflection\Invoker;
use pallo\library\router\Route;

use \Exception;
use \ReflectionParameter;

/**
 * Generic dispatcher for request objects
 */
class GenericDispatcher implements Dispatcher {

    /**
     * Instance of the invoker
     * @var pallo\library\reflection\Invoker;
     */
    protected $invoker;

    /**
     * Flag to see if the callback implements the controller interface
     * @var boolean
     */
    protected $isController;

    /**
     * Request for the dispatch
     * @var pallo\library\mvc\Request
     */
    protected $request;

    /**
     * Response of the dispatch
     * @var pallo\library\mvc\Response
     */
    protected $response;

    /**
     * Route from the request
     * @var pallo\library\router\Route
     */
    protected $route;

    /**
     * Constructs a new dispatcher
     * @return null
     */
    public function __construct(Invoker $invoker) {
        $this->invoker = $invoker;
        $this->request = null;
        $this->response = null;
        $this->route = null;
    }

    /**
     * Dispatches a request to the action of a controller
     * @param pallo\library\mvc\Request $request The request to dispatch
     * @param pallo\library\mvc\Response $response The response to dispatch the request to
     * @return mixed The return value of the action
     * @throws Exception when the action is not invokable
     */
    public function dispatch(Request $request, Response $response) {
        $this->isController = false;
        $this->request = $request;
        $this->response = $response;
        $this->route = $request->getRoute();
        $this->callback = $this->getCallback();
        $this->arguments = $this->getArguments();

        $this->prepareCallback();

        $returnValue = $this->invokeCallback();

        $this->arguments = null;
        $this->callback = null;
        $this->route = null;
        $this->response = null;
        $this->request = null;
        $this->isController = null;

        return $returnValue;
    }

    /**
     * Gets the callback from the route
     * @return pallo\library\Callback
     * @throws pallo\library\mvc\exception\MvcException when the callback in
     * the route is invalid
     */
    protected function getCallback() {
        $callback = $this->route->getCallback();

        try {
            $callback = new Callback($callback);

            $callback = $this->processCallback($callback);

            if (!$callback->isCallable()) {
                throw new MvcException('Could not dispatch action of route ' . $this->route->getId() . ': callback is not callable');
            }
        } catch (Exception $exception) {
            throw new MvcException('Could not dispatch action of route ' . $this->route->getId() . ': callback could not be created', 0, $exception);
        }

        return $callback;
    }

    /**
     * Processes the callback
     * @param Callback $callback Callback to process
     * @return Callback Processed callback
     */
    protected function processCallback(Callback $callback) {
        return $callback;
    }

    /**
     * Gets the arguments for the callback
     * @return array
     */
    protected function getArguments() {
        $arguments = array();

        $routePredefinedArguments = $this->route->getPredefinedArguments();
        foreach ($routePredefinedArguments as $name => $value) {
            $arguments[$name] = $value;
        }

        $routeArguments = $this->route->getArguments();
        foreach ($routeArguments as $name => $value) {
            $arguments[$name] = urldecode($value);
        }

        return $arguments;
    }

    /**
     * Prepares the callback
     * @return null
     */
    protected function prepareCallback() {
        $class = $this->callback->getClass();
        if (!$class || !$class instanceof Controller) {
            // callback is a function or a method on a non Controller instance
            return;
        }

        $this->isController = true;

        $class->setRequest($this->request);
        $class->setResponse($this->response);
    }

    /**
     * Invokes the callback
     * @return mixed Return value of the callback
     */
    protected function invokeCallback() {
        try {
            if (!$this->isController) {
                return $this->invoker->invoke($this->callback, $this->arguments, $this->route->isDynamic());
            }


            $controller = $this->callback->getClass();
            if ($controller->preAction()) {
                $returnValue = $this->invoker->invoke($this->callback, $this->arguments, $this->route->isDynamic());

                $controller->postAction();
            }

            return $returnValue;
        } catch (Exception $exception) {
            throw new MvcException('Could not dispatch action of route ' . $this->route->getId() . ': ' . $this->callback . ' could not be invoked', 0, $exception);
        }
    }

}