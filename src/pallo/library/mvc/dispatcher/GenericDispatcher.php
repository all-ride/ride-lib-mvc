<?php

namespace pallo\library\mvc\dispatcher;

use pallo\library\mvc\controller\Controller;
use pallo\library\mvc\exception\MvcException;
use pallo\library\mvc\Request;
use pallo\library\mvc\Response;
use pallo\library\reflection\Callback;
use pallo\library\reflection\ReflectionHelper;
use pallo\library\router\Route;

use \Exception;
use \ReflectionParameter;

/**
 * Generic dispatcher for request objects
 */
class GenericDispatcher implements Dispatcher {

	/**
	 * Helper for reflection
	 * @var ReflectionHelper
	 */
	protected $reflectionHelper;

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
	public function __construct() {
		$this->reflectionHelper = null;
		$this->request = null;
		$this->response = null;
		$this->route = null;
	}

	/**
	 * Sets the reflection helper
	 * @param ReflectionHelper $reflectionHelper
	 * @return null
	 */
	public function setReflectionHelper(ReflectionHelper $reflectionHelper = null) {
		$this->reflectionHelper = $reflectionHelper;
	}

	/**
	 * Gets the reflection helper
	 * @return ReflectionHelper
	 */
	public function getReflectionHelper() {
		if (!$this->reflectionHelper) {
			$this->reflectionHelper = new ReflectionHelper();
		}

		return $this->reflectionHelper;
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
        $this->preInvokeCallback();
        $returnValue = $this->invokeCallback();
        $this->postInvokeCallback();

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
            if (!$callback->isCallable()) {
                throw new MvcException('Could not dispatch action of route ' . $this->route->getId() . ': callback is not callable');
            }
        } catch (Exception $exception) {
            throw new MvcException('Could not dispatch action of route ' . $this->route->getId() . ': callback could not be created', 0, $exception);
        }

        return $callback;
    }

    /**
     * Gets the arguments for the callback
     * @return array
     */
    protected function getArguments() {
    	$arguments = $this->getReflectionHelper()->getArguments($this->callback);
    	$routeArguments = $this->route->getArguments();
    	$routePredefinedArguments = $this->route->getPredefinedArguments();

    	// parse the route arguments in the method signature
    	foreach ($arguments as $name => $argument) {
    		if (isset($routeArguments[$name])) {
    			$arguments[$name] = urldecode($routeArguments[$name]);

    			unset($routeArguments[$name]);
    		} elseif (isset($routePredefinedArguments[$name])) {
    			$arguments[$name] = $routePredefinedArguments[$name];

    			unset($routePredefinedArguments[$name]);
    		} else {
    			$arguments[$name] = $this->getArgumentValue($argument);
    		}
    	}

    	// validate remaining arguments
    	if ($routeArguments || $routePredefinedArguments) {
    		if ($this->route->isDynamic()) {
    			// add dynamic arguments
	    		foreach ($routePredefinedArguments as $name => $value) {
	    			$arguments[$name] = $value;
	    		}
	    		foreach ($routeArguments as $name => $value) {
	    			$arguments[$name] = $value;
	    		}
    		} else {
    			// invalid arguments provided
	    		$arguments = array();
	    		foreach ($routeArguments as $name => $null) {
	    			$arguments[$name] = true;
	    		}
	    		foreach ($routePredefinedArguments as $name => $null) {
	    			$arguments[$name] = true;
	    		}

	    		throw new MvcException('Could not dispatch action of route ' . $this->route->getId() . ': arguments (' . implode(', ', array_keys($arguments)) . ') provided which are not part of the callback signature');
    		}
    	}

    	return $arguments;
    }

    /**
     * Gets the value for the provided argument
     * @param ReflectionParameter $argument
     * @return mixed
     * @throws pallo\library\mvc\exception\MvcException when the argument could
     * not be retrieved
     */
    protected function getArgumentValue(ReflectionParameter $argument) {
    	if (!$argument->isDefaultValueAvailable()) {
    		throw new MvcException('Could not dispatch action of route ' . $this->route->getId() . ': argument ' . $argument->getName() . ' is required');
    	}

    	return $argument->getDefaultValue();
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
     * Hook straight before invoking the controller
     * @return null
     */
    protected function preInvokeCallback() {

    }

    /**
     * Invokes the callback
     * @return mixed Return value of the callback
     */
    protected function invokeCallback() {
    	if (!$this->isController) {
    		return $this->callback->invokeWithArrayArguments($this->arguments);
    	}

    	$returnValue = null;

        $controller = $this->callback->getClass();
        if ($controller->preAction()) {
            $returnValue = $this->callback->invokeWithArrayArguments($this->arguments);

            $controller->postAction();
        }

        return $returnValue;
    }

    /**
     * Hook straight after invoking the callback
     * @return null
     */
    protected function postInvokeCallback() {

    }

}