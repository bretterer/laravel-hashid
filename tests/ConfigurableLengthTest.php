<?php

use Bretterer\LaravelHashId\Traits\HasHashIds;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ShortHashIdUser extends Model
{
    use HasHashIds;

    public $timestamps = false;

    protected $table = 'short_hash_users';

    protected $guarded = [];

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public function hashIdLength(): int
    {
        return 12;
    }
}

describe('Configurable HashId length', function () {
    beforeEach(function () {
        Schema::dropIfExists('short_hash_users');
        Schema::create('short_hash_users', function ($table) {
            $table->hashId('id', 12)->primary();
            $table->string('name');
        });
    });

    it('generates hashId with custom length', function () {
        $user = ShortHashIdUser::create(['name' => 'Alice']);
        expect($user->id)->toMatch('/^[0-9A-Za-z]{12}$/');
        expect(strlen($user->id))->toBe(12);
    });

    it('validates hashId with custom length', function () {
        $trait = new class
        {
            use HasHashIds;

            public function hashIdLength(): int
            {
                return 12;
            }

            public function test($value)
            {
                return $this->isValidUniqueId($value);
            }
        };

        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $valid = $generator->generate(12);
        expect($trait->test($valid))->toBeTrue();

        // 16-char hashId should fail validation for a 12-length model
        $tooLong = $generator->generate(16);
        expect($trait->test($tooLong))->toBeFalse();
    });

    it('retrieves model by custom-length hashId', function () {
        $user = ShortHashIdUser::create(['name' => 'Bob']);
        $found = ShortHashIdUser::find($user->id);
        expect($found)->not->toBeNull();
        expect($found->name)->toBe('Bob');
    });
});
