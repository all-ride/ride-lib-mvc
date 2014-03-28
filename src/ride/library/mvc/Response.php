<?php

namespace ride\library\mvc;

use ride\library\http\Request as HttpRequest;
use ride\library\http\Response as HttpResponse;
use ride\library\mvc\message\MessageContainer;
use ride\library\mvc\message\Message;
use ride\library\mvc\view\View;

/**
 * An extended HTTP request with messages and a view
 */
class Response extends HttpResponse {

    /**
     * Container for notification messages
     * @var \ride\library\mvc\message\MessageContainer
     */
    protected $messageContainer;

    /**
     * View for this response
     * @var \ride\library\mvc\view\View
     */
    protected $view;

    /**
     * Flag to see if the output buffer should be cleared when sending the
     * response
     * @var boolean
     */
    protected $clearOutputBuffer;

    /**
     * Constructs a new response
     * @return null
     */
    public function __construct() {
        parent::__construct();

        $this->messageContainer = new MessageContainer();
        $this->view = null;
        $this->clearOutputBuffer = true;
    }

    /**
     * Add a message to the response
     * @param \ride\library\mvc\message\Message $message Message to add
     * @return null
     */
    public function addMessage(Message $message) {
        $this->messageContainer->add($message);
    }

    /**
     * Checks if there are messages added to the response
     * @return boolean
     */
    public function hasMessages() {
        return $this->messageContainer->hasMessages();
    }

    /**
     * Gets the message container
     * @return \ride\library\mvc\message\MessageContainer
     */
    public function getMessageContainer() {
        return $this->messageContainer;
    }

    /**
     * Sets the view of this response. A view will override the body when
     * sending the response
     * @param \ride\library\mvc\view\View $view The view
     * @return null
     */
    public function setView(View $view = null) {
        $this->view = $view;
    }

    /**
     * Returns the view of this response.
     * @return \ride\library\mvc\view\View The view
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
     * Sets whether the output buffer will be cleared when sending the response
     * @param boolean $clearOutputBuffer
     * @return null
     */
    public function setClearOutputBuffer($clearOutputBuffer) {
        $this->clearOutputBuffer = $clearOutputBuffer;
    }

    /**
     * Gets whether the output buffer will be cleared when sending the response
     * @return boolean
     */
    public function willClearOutputBuffer() {
        return $this->clearOutputBuffer;
    }

    /**
     * Sends the response to the client
     * @param \ride\library\http\Request $request The request to respond to
     * @return null
     */
    public function send(HttpRequest $request) {
        $this->sendHeaders($request->getProtocol());

        if ($this->willRedirect()) {
            return;
        }

        if ($this->clearOutputBuffer) {
            while (@ob_end_flush());
        }

        if ($this->view) {
            $this->view->render(false);
        } else {
            echo $this->body;
        }
    }

}
