<?php

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    protected function assertException($exceptionName, $exceptionMessage = '', $exceptionCode = 0)
    {
        if (method_exists(parent::class, 'expectException')) {
            parent::expectException($exceptionName);
            parent::expectExceptionMessage($exceptionMessage);
            parent::expectExceptionCode($exceptionCode);
        } else {
            $this->setExpectedException($exceptionName, $exceptionMessage, $exceptionCode);
        }
    }
}
