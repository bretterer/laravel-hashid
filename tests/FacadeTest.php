<?php

use Bretterer\LaravelHashId\Facades\LaravelHashId;

describe('LaravelHashId Facade', function () {
    it('can generate a hashId via facade', function () {
        $hashId = LaravelHashId::generate(16);
        expect($hashId)->toMatch('/^[0-9A-Za-z]{16}$/');
        expect(strlen($hashId))->toBe(16);
    });
});
