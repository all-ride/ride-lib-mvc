<?php

namespace ride\library\mvc\dispatcher;

use ride\library\mvc\Request;
use ride\library\mvc\Response;

/**
 * Interface for a dispatcher of request objects
 */
interface Dispatcher {

    /**
     * Dispatches a request to the callback
     * @param ride\library\mvc\Request $request Request to dispatch
     * @param ride\library\mvc\Response $response Response to dispatch the
     * request to
     * @return mixed Return value of the action
     */
    public function dispatch(Request $request, Response $response);

}