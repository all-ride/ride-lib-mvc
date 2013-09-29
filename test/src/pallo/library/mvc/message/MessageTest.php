<?php

namespace pallo\library\mvc\message;

use \PHPUnit_Framework_TestCase;

class MessageTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $message = 'message';
        $type = 'type';

        $msg = new Message($message, $type);

        $this->assertEquals($message, $msg->getMessage());
        $this->assertEquals($type, $msg->getType());

        $msg = new Message($message);

        $this->assertEquals($message, $msg->getMessage());
        $this->assertNull($msg->getType());
    }

    /**
     * @dataProvider providerConstructWithInvalidValuesThrowsException
     * @expectedException pallo\library\mvc\exception\MvcException
     */
    public function testConstructWithInvalidValuesThrowsException($message, $type) {
        new Message($message, $type);
    }

    public function providerConstructWithInvalidValuesThrowsException() {
        return array(
            array('', 'value'),
            array(null, 'value'),
            array(array(), 'value'),
            array($this, 'value'),
            array('name', ''),
            array('name', array()),
            array('name', $this),
        );
    }

}