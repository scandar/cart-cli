# Cart-CLI

A simple cart command line interface

# Features!
  - take a list of items
  - calculate price
  - calculate taxes
  - apply offers
  - convert to different currencies
  - format output

# Requirements
 - PHP 7.4

## Usage
```sh
php cart create <items> [options]
```
```sh
php cart create t-shirt pants shoes --bill-currency=EGP
```

## Config
### items
You can add items in `config/items.php` all prices are in USD cents in the following format
```php
't-shirt' => ['price' => 1099],
```
to convert from dollars to cents multiply the amount in dollars to 100 `amount in dolars * 100 = amount in cents`

### currencies
you can add as many currencies as you want the default currency is USD with a conversion rate of `1`
any other currency should have a conversion rate equal to it's price in dollars (the equivalent amount to 1 US dollar)
use `config/currencies.php` to add new currencies in the `available` array
```php
[
    'name' => 'usd',
    'conversion_rate' => 1.0,
    'symbol' => '$',
    'format' => 'S#'
],
[
    'name' => 'egp',
    'conversion_rate' => 15.7,
    'symbol' => 'e£',
    'format' => '# S'
],
```

### offers
use `config/offers.php` to add offers
the array key should be the name of the item the user must buy to get the offer
in the following example the user must buy `2 t-shirts` to get a `50% discount` on a single `jacket`
buying 4 t-shirts will get him 50% discount on 2 jackets and so on..
```php
't-shirt' => [
    'should_buy' => 2,
    'discount_percent' => 0.50,
    'item' => 'jacket',
],
```

### taxes
`config/taxes.php` the `vat` key contains the percentage of the value added taxes 
```php
'vat' => 0.14
```

### production build
to have standalone production executable 
```sh
php cart app:build
```
you should be able to find the executable in the following path `builds/cart`
```sh
./builds/cart create shoes t-shirt
```

## Technical Decisions
### framework of choice
I've picked [Laravel-Zero] because it's in my opinion the most fully fledged cli framework out there
it has everything you need to get started writing your own cli from testing to dependency injection to build tools
also it's built on some of laravel's components which makes really easy to use

### project structure
I've used `app/Core/CartService.php` as an interface to be called from the command class, it's sole purpose is to deliver input to responsible `builders` and return output to the command class.

`Builders` are used to build the required models.

`Models` are used to represent the application different data typse in a way that's easy to understand and interact with.

`Traits` are just helper methods extracted away from the cart service to keep everything organized.

`Exceptions` are just empty classes that extend the php exception class just to differentiate between different errors, very helpful in unit tests.

`tests` unit tests are using the same structure as the `app\Core` folder just to keep things organized
i used [Pest] testing framework for the clean simple experience it offers (it's a wrapper for [PHPUnit] that uses callbacks instead of classes which in my opinion is very easier to work with than regular classes).


[Laravel-Zero]: <https://laravel-zero.com/>
[Pest]: <https://pestphp.com/>
[PHPUnit]: <https://phpunit.de/>
