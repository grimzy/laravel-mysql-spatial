<?php

abstract class BaseTestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    protected function assertException($exceptionName)
    {
        if (method_exists(parent::class, 'expectException')) {
            parent::expectException($exceptionName);
        } else {
            $this->setExpectedException($exceptionName);
        }
    }
}
