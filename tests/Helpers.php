<?php

namespace Tests;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

function reflection(Object $object): ReflectionClass
{
    return new ReflectionClass(get_class($object));
}

function privateMethod(ReflectionClass $reflectionClass, string $methodName): ReflectionMethod
{
    $method = $reflectionClass->getMethod($methodName);
    $method->setAccessible(true);
    return $method;
}

function privateProperty(ReflectionClass $reflectionClass, string $propertyName): ReflectionProperty
{
    $prop = $reflectionClass->getProperty($propertyName);
    $prop->setAccessible(true);
    return $prop;
}
