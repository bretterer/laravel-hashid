<?php

namespace Bretterer\LaravelHashId;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class LaravelHashIdServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerBlueprintMacros();
        $this->registerValidationRules();
        $this->registerStrMacros();
    }

    protected function registerBlueprintMacros(): void
    {
        Blueprint::macro('hashId', fn (string $column = 'id', int $length = 16): ColumnDefinition => $this->char($column, $length));

        Blueprint::macro('foreignHashId', fn (string $column, int $length = 16): ColumnDefinition =>
            /** @phpstan-ignore-next-line */
            $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'char',
                'name' => $column,
                'length' => $length,
            ])));

        Blueprint::macro('hashIdMorphs', function (string $name, ?string $indexName = null) {
            /** @var Blueprint $this */
            $this->string("{$name}_type");
            $this->char("{$name}_id", 16);
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });

        Blueprint::macro('nullableHashIdMorphs', function (string $name, ?string $indexName = null) {
            /** @var Blueprint $this */
            $this->string("{$name}_type")->nullable();
            $this->char("{$name}_id", 16)->nullable();
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });
    }

    protected function registerStrMacros(): void
    {
        Str::macro('hashId', fn (int $length = 16): string => (new LaravelHashId)->generate($length));

        Str::macro('isHashId', fn (string $value, ?int $length = null): bool => LaravelHashId::isValid($value, $length));
    }

    protected function registerValidationRules(): void
    {
        $this->app['validator']->extend('hashid', function ($attribute, $value, $parameters) {
            if (! is_string($value)) {
                return false;
            }

            $length = ! empty($parameters[0]) ? (int) $parameters[0] : null;

            return LaravelHashId::isValid($value, $length);
        }, 'The :attribute must be a valid HashId.');
    }
}
