<?php

namespace ride\library\mvc\controller;

use ride\library\mvc\Request;
use ride\library\mvc\Response;

/**
 * Interface for a controller of an action
 */
interface Controller {

    /**
     * Sets the request for this controller
     * @param \ride\library\mvc\Request $request The request
     * @return null
     */
    public function setRequest(Request $request);

    /**
     * Sets the response for this controller
     * @param \ride\library\mvc\Response $response The response
     * @return null
     */
    public function setResponse(Response $response);

    /**
     * Hook to execute before every action
     * @return boolean True to execute the action, false to skip it
     */
    public function preAction();

    /**
     * Hook to execute after every action
     * @return null
     */
    public function postAction();

}