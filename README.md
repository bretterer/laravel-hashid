# Laravel Hashid

Generate and use HashIds for your Laravel models, just like native ULIDs and UUIDs.

![Latest Version on Packagist](https://img.shields.io/packagist/v/bretterer/laravel-hashid.svg?style=flat-square)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/bretterer/laravel-hashid/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/bretterer/laravel-hashid/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/bretterer/laravel-hashid/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/bretterer/laravel-hashid/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bretterer/laravel-hashid.svg?style=flat-square)](https://packagist.org/packages/bretterer/laravel-hashid)


This package allows you to generate and use HashIds for your Laravel models, just like native ULIDs and UUIDs. It is easy to integrate and works seamlessly with Laravel's Eloquent models.



## Installation

Install via composer:

```bash
composer require bretterer/laravel-hashid
```

## Usage

### 1. Add the Trait to Your Model

```php
use Bretterer\LaravelHashId\Traits\HasHashIds;

class User extends Model
{
	use HasHashIds;
	// ...
}
```

### 2. Use HashId Columns in Migrations

```php
Schema::create('users', function ($table) {
	$table->hashId('id', 16)->primary();
	$table->string('name');
});

Schema::create('posts', function ($table) {
	$table->hashId('id', 16)->primary();
	$table->foreignHashId('user_id', 'users', 'id', 16);
	$table->string('title');
});
```

### 3. Creating Models

```php
$user = User::create(['name' => 'Alice']);
echo $user->id; // 16-character base62 HashId

$post = Post::create(['user_id' => $user->id, 'title' => 'Hello']);
```

### 4. Custom Prefixes

```php
class PrefixedUser extends Model {
	use HasHashIds;
	public function idPrefix(): string { return 'usr'; }
}

$user = PrefixedUser::create(['name' => 'Dave']);
echo $user->id; // usr_XXXXXXXXXXXXXXX
```

### 5. HashId Generator

```php
use Bretterer\LaravelHashId\LaravelHashId;

$generator = new LaravelHashId();
$hashId = $generator->generate(16); // Random HashId
$hashIdFromValue = $generator->generateFromValue('value', 'salt', 16); // HashId from value
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.


## Credits

- [Brian Retterer](https://github.com/bretterer)
- [All Contributors](../../contributors)
- Special thanks to [Spatie](https://spatie.be/open-source) for their excellent Laravel package skeleton project.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
