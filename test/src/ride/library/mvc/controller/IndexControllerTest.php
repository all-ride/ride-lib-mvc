<?php

namespace ride\library\mvc\controller;

use ride\library\mvc\Request;
use ride\library\mvc\Response;

use \PHPUnit_Framework_TestCase;

class IndexControllerTest extends PHPUnit_Framework_TestCase {

    public function testIndexAction() {
        $request = new Request('/path');
        $response = new Response();

        $controller = new IndexController();
        $controller->setRequest($request);
        $controller->setResponse($response);

        $this->assertEquals(Response::STATUS_CODE_OK, $response->getStatusCode());
        $this->assertTrue($controller->preAction());

        $controller->indexAction();

        $this->assertNull($controller->postAction());
        $this->assertEquals(Response::STATUS_CODE_NOT_IMPLEMENTED, $response->getStatusCode());
    }

}