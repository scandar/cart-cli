<?php

namespace App\Core\Models;

use Illuminate\Support\Collection;

class Output extends BaseModel
{
    protected string $subtotal;
    protected string $taxes;
    protected Collection $discounts;
    protected string $total;
}
