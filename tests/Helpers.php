<?php

namespace Tests;

use ReflectionClass;

function reflection(Object $object): ReflectionClass
{
    return new ReflectionClass(get_class($object));
}
