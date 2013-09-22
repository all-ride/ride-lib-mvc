<?php

namespace pallo\library\mvc;

use pallo\library\http\Request as HttpRequest;
use pallo\library\http\Response as HttpResponse;
use pallo\library\mvc\view\View;

/**
 * A extension of the HTTP request with view
 */
class Response extends HttpResponse {

    /**
     * The view for this response
     * @var pallo\core\view\View
     */
    protected $view;

    /**
     * Sets the view of this response. A view will override the body when
     * sending the response
     * @param pallo\core\view\View $view The view
     * @return null
     */
    public function setView(View $view = null) {
        $this->view = $view;
    }

    /**
	 * Returns the view of this response.
	 * @return pallo\core\view\View The view
	 */
    public function getView() {
        return $this->view;
    }

    /**
     * Returns the body of this response
     * @return string The body
     */
    public function getBody() {
        if ($this->view) {
            return $this->view->render(true);
        }

        return $this->body;
    }

    /**
     * Sets the response status code to not modified and removes illegal
     * headersfor such a response code
     * @return null
     */
    public function setNotModified() {
    	parent::setNotModified();

    	$this->setView(null);
    }

    /**
     * Sends the response to the client
     * @param pallo\library\http\Request $request The request to respond to
     * @return null
     */
    public function send(HttpRequest $request) {
        $this->sendHeaders($request->getProtocol());

        if ($this->willRedirect()) {
            return;
        }

        if ($this->view) {
            $this->view->render(false);
        } else {
            echo $this->body;
        }
    }

}