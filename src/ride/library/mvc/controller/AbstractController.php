<?php

namespace ride\library\mvc\controller;

use ride\library\mvc\Request;
use ride\library\mvc\Response;

/**
 * Abstract implementation of a controller
 */
abstract class AbstractController implements Controller {

    /**
     * The request for this controller
     * @var \ride\library\mvc\Request
     */
    protected $request;

    /**
     * The response for this controller
     * @var \ride\library\mvc\Response
     */
    protected $response;

    /**
     * Sets the request for this controller
     * @param \ride\library\mvc\Request $request The request
     * @return null
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * Gets the request from this controller
     * @return \ride\library\mvc\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Sets the response for this controller
     * @param \ride\library\mvc\Response $response The response
     * @return null
     */
    public function setResponse(Response $response) {
        $this->response = $response;
    }

    /**
     * Gets the response from this controller
     * @return \ride\library\mvc\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Hook to execute before every action
     * @return boolean True to execute the action, false to skip it
     */
    public function preAction() {
        return true;
    }

    /**
     * Hook to execute after every action
     * @return null
     */
    public function postAction() {

    }

}
