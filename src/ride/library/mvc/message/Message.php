<?php

namespace ride\library\mvc\message;

use ride\library\mvc\exception\MvcException;

/**
 * Message data container
 */
class Message {

    /**
     * Type for a error message
     * @var string
     */
    const TYPE_ERROR = 'error';

    /**
     * Type for a information message
     * @var string
     */
    const TYPE_INFORMATION = 'info';

    /**
     * Type for a success message
     * @var string
     */
    const TYPE_SUCCESS = 'success';

    /**
     * Type for a warning message
     * @var string
     */
    const TYPE_WARNING = 'warning';

    /**
     * Actual message
     * @var string
     */
    protected $message;

    /**
     * Type of the message
     * @var string
     */
    protected $type;

    /**
     * Construct a new message
     * @param string $message the message
     * @param string $type type of the message
     * @return null
     */
    public function __construct($message, $type = null) {
        $this->setMessage($message);
        $this->setType($type);
    }

    /**
     * Sets the message
     * @param string $message
     * @return null
     * @throws \ride\library\mvc\exception\MvcException when the provided
     * message is empty or invalid
     */
    public function setMessage($message) {
        if (!is_string($message) || !$message) {
            throw new MvcException('Could not set the message: provided message is invalid or empty');
        }

        $this->message = $message;
    }

    /**
     * Gets the message
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set the type of this message
     * @param string $type
     * @return null
     * @throws \ride\library\mvc\exception\MvcException when the provided
     * message is null or not a string
     */
    public function setType($type = null) {
        if ($type !== null && (!is_string($type) || !$type)) {
            throw new MvcException('Could not set the type: provided type is invalid or empty');
        }

        $this->type = $type;
    }

    /**
     * Get the type of this message
     * @return string
     */
    public function getType() {
        return $this->type;
    }

}