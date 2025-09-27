<?php

describe('HasHashIds uncovered line', function () {
    it('isValidUniqueId returns false for non-string', function () {
        $trait = new class
        {
            use \Bretterer\LaravelHashId\Traits\HasHashIds;

            public function test($value)
            {
                return $this->isValidUniqueId($value);
            }
        };
        expect($trait->test(123))->toBeFalse();
        expect($trait->test([]))->toBeFalse();
        expect($trait->test(null))->toBeFalse();
    });
});
