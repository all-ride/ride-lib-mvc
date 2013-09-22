<?php

namespace pallo\library\mvc;

use pallo\library\http\Header;

use \PHPUnit_Framework_TestCase;

class ResponseTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var pallo\library\mvc\Response
	 */
	protected $response;

	protected function setUp() {
		parent::setUp();

		$this->response = new Response();
	}

	public function testView() {
		$this->assertNull($this->response->getView());

		$view = $this->getMock('pallo\\library\\mvc\\view\\View', array('render'));
		$view->expects($this->once())->method('render')->with($this->equalTo(true))->will($this->returnValue('view'));

		$this->response->setView($view);
		$this->response->setBody('body');

		$this->assertEquals($view, $this->response->getView());
		$this->assertEquals('view', $this->response->getBody());

		$this->response->setView(null);

		$this->assertNull($this->response->getView());
		$this->assertEquals('body', $this->response->getBody());

		$this->response->setView($view);
		$this->response->setNotModified();

		$this->assertNull($this->response->getView());
		$this->assertEquals(null, $this->response->getBody());
	}

	public function testSend() {
		$this->expectOutputString('body');

		$view = $this->getMock('pallo\\library\\mvc\\view\\View', array('render'));
		$view->expects($this->once())->method('render')->with($this->equalTo(false));

		$this->response->setView($view);
		$this->response->setBody('body');
		$this->response->removeHeader(Header::HEADER_DATE);

		$request = new Request('/');

		$this->response->send($request);

		$this->response->setView(null);

		$this->response->send($request);
	}

}