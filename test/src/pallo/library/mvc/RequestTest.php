<?php

namespace pallo\library\mvc;

use pallo\library\router\Route;

use \PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase {

	public function testRoute() {
		$request = new Request('/path');

		$this->assertNull($request->getRoute());

		$route = new Route('/path', 'callback');
		$request->setRoute($route);

		$this->assertEquals($route, $request->getRoute());
	}

	public function testPath() {
		$request = new Request('/path/index.php/foo/bar');

		$this->assertEquals('/foo/bar', $request->getBasePath());
		$this->assertEquals('http://localhost/path/index.php', $request->getBaseScript());
		$this->assertEquals('http://localhost/path', $request->getBaseUrl());

		$request = new Request('/path/index.php');

		$this->assertEquals('/', $request->getBasePath());
		$this->assertEquals('http://localhost/path/index.php', $request->getBaseScript());
		$this->assertEquals('http://localhost/path', $request->getBaseUrl());

		$_SERVER['SCRIPT_NAME'] = '/path/index.php';
		$request = new Request('/path/foo/bar');

		$this->assertEquals('/foo/bar', $request->getBasePath());
		$this->assertEquals('http://localhost/path', $request->getBaseScript());
		$this->assertEquals('http://localhost/path', $request->getBaseUrl());

		$_SERVER['SCRIPT_NAME'] = 'console.php';
		$request = new Request('/path/foo/bar');

		$this->assertEquals('/path/foo/bar', $request->getBasePath());
		$this->assertEquals('http://localhost', $request->getBaseScript());
		$this->assertEquals('http://localhost', $request->getBaseUrl());

		unset($_SERVER['SCRIPT_NAME']);
		$request = new Request('/path/foo/bar');

		$this->assertEquals('/path/foo/bar', $request->getBasePath());
		$this->assertEquals('http://localhost', $request->getBaseScript());
		$this->assertEquals('http://localhost', $request->getBaseUrl());
	}

}