<?php

namespace pallo\library\mvc\dispatcher;

use pallo\library\mvc\Request;
use pallo\library\mvc\Response;
use pallo\library\reflection\ReflectionHelper;
use pallo\library\router\Route;

use \PHPUnit_Framework_TestCase;

class GenericDispatcherTest extends PHPUnit_Framework_TestCase {

    private $dispatcher;

    protected function setUp() {
        $this->dispatcher = new GenericDispatcher();
    }

    public function testDispatch() {
    	$this->dispatcher->setReflectionHelper(new ReflectionHelper());
        $actionName = 'testAction';
        $path = '/test';

        $controllerClass = 'pallo\\library\\mvc\\controller\\AbstractController';
        $controllerActions = array($actionName, 'setRequest', 'setResponse', 'preAction', 'postAction');

        $controllerMock = $this->getMock($controllerClass, $controllerActions);
        $controllerMockActionCall = $controllerMock->expects($this->once());
        $controllerMockActionCall->method($actionName);
        $controllerMockActionCall->will($this->returnValue(null));
        $controllerMockActionCall = $controllerMock->expects($this->once());
        $controllerMockActionCall->method('preAction');
        $controllerMockActionCall->will($this->returnValue(true));

        $route = new Route($path, array($controllerMock, $actionName));

        $request = new Request($path);
        $request->setRoute($route);
        $response = new Response();

        $result = $this->dispatcher->dispatch($request, $response);

        $this->assertNull($result);
    }

    public function testDispatchWithArguments() {
        $route = new Route('/path/%id%', array($this, 'actionTest'));
        $route->setPredefinedArguments(array(
        	'action' => 'content',
        ));
        $route->setArguments(array(
        	'id' => 7,
        ));

        $request = new Request('/path/content/7');
        $request->setRoute($route);
        $response = new Response();

        $result = $this->dispatcher->dispatch($request, $response);

        $this->assertEquals('action-content-7-1', $result);
    }

    public function testDispatchWithDynamicArguments() {
        $route = new Route('/path/%id%', array($this, 'actionTest'));
        $route->setPredefinedArguments(array(
        	'action' => 'content',
        	'foo' => 'bar',
        ));
        $route->setArguments(array(
        	'id' => '7',
        	'10',
        	'2',
        ));
        $route->setIsDynamic(true);

        $request = new Request('/path/content/7/10/2');
        $request->setRoute($route);
        $response = new Response();

        $result = $this->dispatcher->dispatch($request, $response);

        $this->assertEquals('action-content-7-1-bar-10-2', $result);
    }

    /**
     * @dataProvider providerDispatchWithArgumentsThrowsExceptionWhenInvalidArgumentsProvided
     * @expectedException pallo\library\mvc\exception\MvcException
     */
    public function testDispatchWithArgumentsThrowsExceptionWhenInvalidArgumentsProvided($predefinedArguments, $arguments) {
        $route = new Route('/path', array($this, 'actionTest'));
        $route->setPredefinedArguments($predefinedArguments);
        $route->setArguments($arguments);

        $request = new Request('/path/content/7');
        $request->setRoute($route);
        $response = new Response();

        $result = $this->dispatcher->dispatch($request, $response);
    }

    public function providerDispatchWithArgumentsThrowsExceptionWhenInvalidArgumentsProvided() {
    	return array(
    		array(array(), array()),
    		array(array('action' => 'content', 'var' => 'value'), array()),
    		array(array('action' => 'content'), array('var' => 'value')),
    		array(array('action' => 'content', 'var' => 'value'), array('var2' => 'value')),
    	);
    }

    /**
     * @dataProvider providerDispatchThrowsExceptionWhenInvalidCallbackProvided
     * @expectedException pallo\library\mvc\exception\MvcException
     */
    public function testDispatchThrowsExceptionWhenInvalidCallbackProvided($callback) {
        $route = new Route('/path', $callback);

        $request = new Request('/path');
        $request->setRoute($route);
        $response = new Response();

        $result = $this->dispatcher->dispatch($request, $response);
    }

    public function providerDispatchThrowsExceptionWhenInvalidCallbackProvided() {
    	return array(
    		array($this),
    		array(array($this, 'unexistantMethod')),
    	);
    }

    public function actionTest($action, $id = null, $option = 1) {
    	$arguments = func_get_args();

    	return 'action-' . implode('-', $arguments);
    }

}