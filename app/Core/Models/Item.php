<?php

namespace App\Core\Models;

class Item extends BaseModel
{
    public string $name;
    protected int $price;
    public ?Offer $offer = null;
}
