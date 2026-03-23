<?php

use Bretterer\LaravelHashId\LaravelHashId;

describe('Testing fakes', function () {
    afterEach(function () {
        LaravelHashId::createNormally();
    });

    describe('freeze', function () {
        it('returns the same hashId on every call', function () {
            $frozen = LaravelHashId::freeze();

            $generator = new LaravelHashId;
            expect($generator->generate())->toBe($frozen);
            expect($generator->generate())->toBe($frozen);
            expect($generator->generate())->toBe($frozen);
        });

        it('accepts a callback and resets after', function () {
            LaravelHashId::freeze(function ($frozen) {
                $generator = new LaravelHashId;
                expect($generator->generate())->toBe($frozen);
            });

            // After callback, generation should be normal (non-frozen)
            $generator = new LaravelHashId;
            $a = $generator->generate();
            $b = $generator->generate();
            expect($a)->not->toBe($b);
        });

        it('resets even if callback throws', function () {
            try {
                LaravelHashId::freeze(function () {
                    throw new RuntimeException('test');
                });
            } catch (RuntimeException) {
                // expected
            }

            $generator = new LaravelHashId;
            $a = $generator->generate();
            $b = $generator->generate();
            expect($a)->not->toBe($b);
        });
    });

    describe('createUsing', function () {
        it('uses custom factory for generation', function () {
            LaravelHashId::createUsing(fn (int $length) => str_repeat('A', $length));

            $generator = new LaravelHashId;
            expect($generator->generate(8))->toBe('AAAAAAAA');
            expect($generator->generate(16))->toBe('AAAAAAAAAAAAAAAA');
        });
    });

    describe('createUsingSequence', function () {
        it('returns values from sequence in order', function () {
            LaravelHashId::createUsingSequence(['first123456789', 'second12345678', 'third123456789']);

            $generator = new LaravelHashId;
            expect($generator->generate())->toBe('first123456789');
            expect($generator->generate())->toBe('second12345678');
            expect($generator->generate())->toBe('third123456789');
        });

        it('falls back to normal generation when sequence is exhausted', function () {
            LaravelHashId::createUsingSequence(['only1234567890']);

            $generator = new LaravelHashId;
            expect($generator->generate())->toBe('only1234567890');

            // Next call should generate a real hashId
            $next = $generator->generate(16);
            expect($next)->toMatch('/^[0-9A-Za-z]{16}$/');
        });

        it('falls back to whenMissing callback when sequence is exhausted', function () {
            LaravelHashId::createUsingSequence(
                ['first123456789'],
                fn (int $length) => str_repeat('X', $length),
            );

            $generator = new LaravelHashId;
            expect($generator->generate())->toBe('first123456789');
            expect($generator->generate(16))->toBe('XXXXXXXXXXXXXXXX');
        });
    });

    describe('createNormally', function () {
        it('resets all fakes', function () {
            LaravelHashId::createUsing(fn () => 'fake');
            LaravelHashId::createNormally();

            $generator = new LaravelHashId;
            expect($generator->generate(16))->not->toBe('fake');
            expect($generator->generate(16))->toMatch('/^[0-9A-Za-z]{16}$/');
        });
    });
});
