<?php

namespace App\Core\Models;

class Item extends BaseModel
{
    public string $name;
    public int $price;
    public ?Offer $offer = null;
}
