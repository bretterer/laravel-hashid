<?php

use Illuminate\Support\Facades\Validator;

describe('hashid validation rule', function () {
    it('passes for valid base62 string', function () {
        $validator = Validator::make(
            ['id' => 'Abc123DEFghi4567'],
            ['id' => 'hashid'],
        );

        expect($validator->passes())->toBeTrue();
    });

    it('fails for non-base62 characters', function () {
        $validator = Validator::make(
            ['id' => 'invalid-id!@#$%^'],
            ['id' => 'hashid'],
        );

        expect($validator->fails())->toBeTrue();
    });

    it('fails for non-string values', function () {
        $validator = Validator::make(
            ['id' => 12345],
            ['id' => 'hashid'],
        );

        expect($validator->fails())->toBeTrue();
    });

    it('validates with specific length parameter', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $hashId = $generator->generate(16);

        $validator = Validator::make(
            ['id' => $hashId],
            ['id' => 'hashid:16'],
        );

        expect($validator->passes())->toBeTrue();
    });

    it('fails when length does not match', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $hashId = $generator->generate(12);

        $validator = Validator::make(
            ['id' => $hashId],
            ['id' => 'hashid:16'],
        );

        expect($validator->fails())->toBeTrue();
    });

    it('passes without length for any base62 string', function () {
        $validator = Validator::make(
            ['id' => 'abc'],
            ['id' => 'hashid'],
        );

        expect($validator->passes())->toBeTrue();
    });

    it('provides correct error message', function () {
        $validator = Validator::make(
            ['user_id' => '!!!'],
            ['user_id' => 'hashid'],
        );

        $validator->fails();
        expect($validator->errors()->first('user_id'))->toBe('The user id must be a valid HashId.');
    });
});
