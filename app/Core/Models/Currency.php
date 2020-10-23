<?php

namespace App\Core\Models;

class Currency extends BaseModel
{
    protected string $name;
    protected string $symbol;
    protected string $format;
    protected float $conversionRate;
}
