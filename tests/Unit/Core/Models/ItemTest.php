<?php

use App\Core\Models\Item;
use App\Exceptions\InvalidPropertyException;

use function Pest\Faker\faker;

it('creates an item', function () {
    $name = faker()->word;
    $price = faker()->randomNumber();
    $item = new Item(['name' => $name, 'price' => $price]);

    expect($item->name)->toBe($name);
    expect($item->price)->toBe($price);
    expect($item->foo)->toBe(null);
});

it('throws an exception when invalid properties provided', function () {
    $this->expectException(InvalidPropertyException::class);
    new Item(['foo' => 'bar']);
});

it('sets a property', function () {
    $item = new Item(['name' => faker()->word, 'price' => faker()->randomNumber]);

    $name = faker()->word;
    $price = faker()->randomNumber();
    $item->setProp('name', $name);
    $item->setProp('price', $price);
    expect($item->name)->toBe($name);
    expect($item->price)->toBe($price);
});
