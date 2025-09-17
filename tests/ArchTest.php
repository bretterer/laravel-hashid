<?php

if (version_compare(PHP_VERSION, '8.3.0', '>=') || getenv('PREFER_LOWEST')) {
    test('skipped arch test on PHP 8.3+/prefer-lowest', function () {
        $this->markTestSkipped('Architecture test skipped due to PHPUnit/Testbench incompatibility.');
    });
} else {
    arch('it will not use debugging functions')
        ->expect(['dd', 'dump', 'ray'])
        ->each->not->toBeUsed();
}
