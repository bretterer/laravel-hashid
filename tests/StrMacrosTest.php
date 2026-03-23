<?php

use Illuminate\Support\Str;

describe('Str::hashId()', function () {
    it('generates a 16-character hashId by default', function () {
        $hashId = Str::hashId();
        expect($hashId)->toMatch('/^[0-9A-Za-z]{16}$/');
    });

    it('generates a hashId with custom length', function () {
        $hashId = Str::hashId(12);
        expect($hashId)->toMatch('/^[0-9A-Za-z]{12}$/');
    });

    it('generates unique values', function () {
        expect(Str::hashId())->not->toBe(Str::hashId());
    });
});

describe('Str::isHashId()', function () {
    it('returns true for valid base62 string', function () {
        expect(Str::isHashId('Abc123DEFghi4567'))->toBeTrue();
    });

    it('returns false for non-base62 characters', function () {
        expect(Str::isHashId('invalid-id!!'))->toBeFalse();
    });

    it('validates with specific length', function () {
        $hashId = Str::hashId(16);
        expect(Str::isHashId($hashId, 16))->toBeTrue();
        expect(Str::isHashId($hashId, 12))->toBeFalse();
    });
});
