<?php

use App\Core\Models\BaseModel;
use App\Exceptions\InvalidPropertyException;

use function Pest\Faker\faker;

class Foo extends BaseModel
{
    protected $foo;
    protected $bar;
    protected $baz;
}

it('create a model', function () {
    $data = [
        'foo' => faker()->word,
        'bar' => faker()->word,
        'baz' => faker()->word,
    ];

    $model = new Foo($data);
    expect($model->foo)->toBe($data['foo']);
    expect($model->bar)->toBe($data['bar']);
    expect($model->baz)->toBe($data['baz']);
    expect($model->boo)->toBeNull();
});


it('throws an exception when invalid properties provided', function () {
    $this->expectException(InvalidPropertyException::class);
    new Foo(['boo' => 'boo']);
});

it('sets a property', function () {
    $item = new Foo(['foo' => faker()->word, 'bar' => faker()->randomNumber]);

    $foo = faker()->word;
    $bar = faker()->randomNumber();
    $item->setProp('foo', $foo);
    $item->setProp('bar', $bar);
    expect($item->foo)->toBe($foo);
    expect($item->bar)->toBe($bar);
});
