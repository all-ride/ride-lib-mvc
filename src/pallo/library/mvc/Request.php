<?php

namespace pallo\library\mvc;

use pallo\library\http\Request as HttpRequest;
use pallo\library\router\Route;

/**
 * A extension of the HTTP request with route and URL functionality
 */
class Request extends HttpRequest {

    /**
     * Route of this request
     * @var pallo\library\router\Route
     */
    protected $route;

    /**
     * Base URL of the request to path
     * @var string
     */
    protected $baseUrl;

    /**
     * Base URL of the request to the main PHP script
     * @var string
     */
    protected $baseScript;

    /**
     * Request path on the base URL
     * @var string
     */
    protected $basePath;

    /**
     * Sets the selected route
     * @param pallo\library\router\Route $route
     * @return null
     */
	public function setRoute(Route $route) {
		$this->route = $route;
	}

    /**
     * Gets the selected route
     * @return pallo\library\router\Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Sets the requested path
     * @param string $path The requested path
     * @throws pallo\library\http\exception\HttpException when an invalid path
     * is provided
     */
    protected function setPath($path) {
    	parent::setPath($path);

	    $positionPhp = strpos($path, 'index.php');
	    if ($positionPhp !== false) {
	    	// a php script in the request
	    	$positionParent = strrpos(substr($path, 0, $positionPhp), '/');
	    	if ($positionParent !== false) {
	    		$baseUrl = substr($path, 0, $positionParent);
	    	}

	    	$baseScript = substr($path, 0, $positionPhp + 9);
	    } elseif (isset($_SERVER['SCRIPT_NAME'])) {
	    	// no php script in the request
	    	$position = strrpos($_SERVER['SCRIPT_NAME'], '/');
	    	if ($position !== false) {
	    		$baseUrl = substr($_SERVER['SCRIPT_NAME'], 0, $position);
	    		$baseScript = $baseUrl;
	    	} else {
	    		// cli
	    		$baseUrl = '/';
	    		$baseScript = null;
	    	}
	    } else {
    		$baseUrl = '/';
    		$baseScript = null;
	    }

	    $server = $this->getServerUrl();

	    $this->baseUrl = rtrim($server . $baseUrl, '/');
	    $this->baseScript = rtrim($server . $baseScript, '/');
	    $this->basePath = rtrim(str_replace($this->baseUrl, '', str_replace($this->baseScript, '', $server . $path)), '/');
	    if (!$this->basePath) {
	    	$this->basePath = '/';
	    }
    }

    /**
     * Gets the path on the running script
     * @return string
     */
    public function getBasePath() {
    	return $this->basePath;
    }

    /**
     * Gets the base URL to the running script
     * @return string
     */
    public function getBaseScript() {
    	return $this->baseScript;
    }

    /**
     * Gets the base URL to the application
     * @return string
     */
    public function getBaseUrl() {
    	return $this->baseUrl;
    }

}