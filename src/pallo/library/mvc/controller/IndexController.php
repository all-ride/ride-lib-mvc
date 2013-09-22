<?php

namespace pallo\library\mvc\controller;

use pallo\library\http\Response;

/**
 * A default controller
 */
class IndexController extends AbstractController {

    /**
     * Default action, sets the status code to not implemented
     * @return null
     */
    public function indexAction() {
        $this->response->setStatusCode(Response::STATUS_CODE_NOT_IMPLEMENTED);
    }

}