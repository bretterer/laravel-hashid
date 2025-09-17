<?php

describe('LaravelHashId uncovered lines', function () {
    it('base62Encode returns "0" for zero bytes', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $result = $generator->base62Encode("\x00\x00\x00");
        expect($result)->toBe('0');
    });

    it('base62Encode returns correct base62 for known value', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        // 1 byte: 0x01 should be '1'
        $result = $generator->base62Encode("\x01");
        expect($result)->toBe('1');
    });

    it('generate pads hashId if too short', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        // Use a length that will likely result in a short base62 string
        $hashId = $generator->generate(1); // base62Encode of 1 byte is usually 1 char
        expect(strlen($hashId))->toBe(1);
    });

    it('generate truncates hashId if too long', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        // Use a length that will result in a long base62 string
        $hashId = $generator->generate(64); // base62Encode of 64 bytes is long
        expect(strlen($hashId))->toBe(64);
    });

    it('generateFromValue pads hashId if too short', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $hashId = $generator->generateFromValue('x', null, 1);
        expect(strlen($hashId))->toBe(1);
    });

    it('generateFromValue truncates hashId if too long', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $hashId = $generator->generateFromValue('x', null, 64);
        expect(strlen($hashId))->toBe(64);
    });
});
