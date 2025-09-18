<?php

namespace Bretterer\LaravelHashId\Tests;

use Bretterer\LaravelHashId\LaravelHashIdServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Bretterer\\LaravelHashId\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Illuminate\Foundation\Exceptions\Handler::class
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelHashIdServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

    }
}
