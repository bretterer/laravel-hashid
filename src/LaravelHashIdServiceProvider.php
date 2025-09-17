<?php

namespace Bretterer\LaravelHashId;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelHashIdServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-hashid');
    }

    public function boot(): void
    {
        parent::boot();

        Blueprint::macro('hashId', fn (string $column = 'id', int $length = 24): ColumnDefinition => $this->char($column, $length));

        Blueprint::macro('foreignHashId', fn (string $column, string $foreignTable, string $foreignColumn = 'id', int $length = 24): ColumnDefinition =>
            /** @phpstan-ignore-next-line */
            $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'char',
                'name' => $column,
                'length' => $length,
            ])));
    }
}
