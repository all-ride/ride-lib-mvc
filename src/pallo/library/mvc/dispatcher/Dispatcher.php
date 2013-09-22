<?php

namespace pallo\library\mvc\dispatcher;

use pallo\library\mvc\Request;
use pallo\library\mvc\Response;

/**
 * Interface for a dispatcher of request objects
 */
interface Dispatcher {

    /**
     * Dispatches a request to the callback
     * @param pallo\library\mvc\Request $request Request to dispatch
     * @param pallo\library\mvc\Response $response Response to dispatch the
     * request to
     * @return mixed Return value of the action
     */
    public function dispatch(Request $request, Response $response);

}