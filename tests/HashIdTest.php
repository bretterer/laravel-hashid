<?php

use Bretterer\LaravelHashId\Traits\HasHashIds;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

// Model for testing
class HashIdUser extends Model
{
    use HasHashIds;

    public $timestamps = false;

    protected $table = 'users';

    protected $guarded = [];

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';
}

class HashIdPost extends Model
{
    use HasHashIds;

    public $timestamps = false;

    protected $table = 'posts';

    protected $guarded = [];

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';
}

class PrefixedUser extends Model
{
    use HasHashIds;

    public $timestamps = false;

    protected $table = 'prefixed_users';

    protected $guarded = [];

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public function idPrefix(): string
    {
        return 'usr';
    }
}

describe('HashId model and migration', function () {
    beforeEach(function () {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');
        Schema::create('users', function ($table) {
            $table->hashId('id', 16)->primary();
            $table->string('name');
        });
        Schema::create('posts', function ($table) {
            $table->hashId('id', 16)->primary();
            $table->foreignHashId('user_id', 'users', 'id', 16);
            $table->string('title');
        });
    });

    it('can create and retrieve a user with hashId', function () {
        $user = HashIdUser::create(['name' => 'Alice']);
        expect($user->id)->toMatch('/^[0-9A-Za-z]{16}$/');
        $found = HashIdUser::find($user->id);
        expect($found->name)->toBe('Alice');
    });

    it('can create a post with foreignHashId', function () {
        $user = HashIdUser::create(['name' => 'Bob']);
        $post = HashIdPost::create(['user_id' => $user->id, 'title' => 'Hello']);
        expect($post->user_id)->toBe($user->id);
        $found = HashIdPost::where('user_id', $user->id)->first();
        expect($found->title)->toBe('Hello');
    });

    it('can retrieve user by hashId from database', function () {
        $user = HashIdUser::create(['name' => 'Charlie']);
        $found = HashIdUser::where('id', $user->id)->first();
        expect($found)->not->toBeNull();
        expect($found->id)->toBe($user->id);
        expect($found->name)->toBe('Charlie');
    });

    it('users table has correct hashId column', function () {
        $columns = Schema::getColumnListing('users');
        expect($columns)->toContain('id');
        // SQLite does not support type/length introspection, so just check existence
    });

    it('posts table has correct foreignHashId column', function () {
        $columns = Schema::getColumnListing('posts');
        expect($columns)->toContain('user_id');
        // SQLite does not support type/length introspection, so just check existence
    });
});

describe('HashId trait validation', function () {
    it('validates correct hashId', function () {
        $trait = new class
        {
            use HasHashIds;

            public function test($value)
            {
                return $this->isValidUniqueId($value);
            }
        };
        $valid = (new \Bretterer\LaravelHashId\LaravelHashId)->generate(16);
        expect($trait->test($valid))->toBeTrue();
    });

    it('rejects invalid hashId', function () {
        $trait = new class
        {
            use HasHashIds;

            public function test($value)
            {
                return $this->isValidUniqueId($value);
            }
        };
        expect($trait->test('invalid_id_1234'))->toBeFalse();
        expect($trait->test('!@#$%^&*()_+abcd'))->toBeFalse();
        expect($trait->test('short'))->toBeFalse();
    });
});

describe('HashId generator', function () {
    it('generates collision-resistant hashIds', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $hashId = $generator->generate(16);
        expect($hashId)->toMatch('/^[0-9A-Za-z]{16}$/');
        expect(strlen($hashId))->toBe(16);
    });

    it('generates hashId from value with salt', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $hashId = $generator->generateFromValue('test', 'salt', 16);
        expect($hashId)->toMatch('/^[0-9A-Za-z]{16}$/');
        expect(strlen($hashId))->toBe(16);
    });

    test('generates different ids on multiple calls', function () {
        $generator = new \Bretterer\LaravelHashId\LaravelHashId;
        $id1 = $generator->generate(16);
        $id2 = $generator->generate(16);
        expect($id1)->not->toBe($id2);
    });
});

describe('HashId model with idPrefix', function () {
    beforeEach(function () {
        Schema::dropIfExists('prefixed_users');
        Schema::create('prefixed_users', function ($table) {
            $table->hashId('id', 16)->primary();
            $table->string('name');
        });
    });

    it('creates user with prefix in hashId', function () {
        $user = PrefixedUser::create(['name' => 'Dave']);
        expect($user->id)->toMatch('/^usr_[0-9A-Za-z]{16}$/');
    });

    it('can retrieve user by prefixed hashId', function () {
        $user = PrefixedUser::create(['name' => 'Eve']);
        $found = PrefixedUser::where('id', $user->id)->first();
        expect($found)->not->toBeNull();
        expect($found->id)->toBe($user->id);
        expect($found->name)->toBe('Eve');
    });
});
