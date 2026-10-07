<?php

namespace Negotiation\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @param class-string $class
     * @param list<mixed> $params
     */
    protected function call_private_method(string $class, string $method, object $object, array $params): mixed
    {
        $method = new \ReflectionMethod($class, $method);

        return $method->invokeArgs($object, $params);
    }
}
