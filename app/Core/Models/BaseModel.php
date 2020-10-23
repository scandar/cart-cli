<?php

namespace App\Core\Models;

use App\Exceptions\InvalidPropertyException;

class BaseModel
{
    public function __construct(array $props)
    {
        foreach ($props as $name => $value) {
            $this->setProp($name, $value);
        }
    }

    public function setProp(string $name, $value): void
    {
        if (!property_exists(get_class($this), $name)) {
            throw new InvalidPropertyException("Invalid propery {$name}");
        }

        $this->$name = $value;
    }

    public function __get(string $name)
    {
        if (property_exists(get_class($this), $name)) {
            return $this->$name;
        }

        return null;
    }
}
