<?php

declare(strict_types=1);

namespace Bretterer\LaravelHashId\Traits;

use Bretterer\LaravelHashId\LaravelHashId;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;

trait HasHashIds
{
    use HasUniqueStringIds;

    public function idPrefix(): string
    {
        return '';
    }

    /**
     * Generate a new unique key for the model.
     */
    public function newUniqueId(): string
    {
        $hashId = (new LaravelHashId)->generate(16);

        return empty($this->idPrefix()) ? $hashId : $this->idPrefix().'_'.$hashId;
    }

    /**
     * Determine if given key is valid.
     */
    protected function isValidUniqueId(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $value = collect(explode('_', $value))->last();

        // Validate it's a 16-character base62 string
        return is_string($value)
            && mb_strlen($value) === 16
            && preg_match('/^[0-9A-Za-z]{16}$/', $value) === 1;
    }
}
