<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Unit;

use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class BaseTestCase extends Orchestra
{
    public function tearDown(): void
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
