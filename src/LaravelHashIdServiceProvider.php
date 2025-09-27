<?php

namespace Bretterer\LaravelHashId;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\ServiceProvider;

class LaravelHashIdServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
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
