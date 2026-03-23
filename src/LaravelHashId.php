<?php

namespace Bretterer\LaravelHashId;

use Closure;

class LaravelHashId
{
    protected static ?Closure $customFactory = null;

    protected static ?array $sequence = null;

    protected static int $sequenceIndex = 0;

    /**
     * Generate a collision-resistant hashId.
     *
     * @param  int  $length  Length of the hashId (default: 16)
     */
    public function generate(int $length = 16): string
    {
        if (static::$sequence !== null) {
            return static::nextInSequence();
        }

        if (static::$customFactory !== null) {
            return (static::$customFactory)($length);
        }

        // Use random_bytes for cryptographic randomness
        $bytes = random_bytes($length);
        // Encode as base62 for compactness and URL safety
        $hashId = $this->base62Encode($bytes);
        // Ensure the hashId is exactly $length characters
        if (strlen($hashId) > $length) {
            $hashId = substr($hashId, 0, $length);
        }

        return $hashId;
    }

    /**
     * Generate a hashId from a given value (with salt for collision resistance).
     *
     * @param  string|int  $value
     */
    public function generateFromValue($value, ?string $salt = null, int $length = 16): string
    {
        $salt = $salt ?? bin2hex(random_bytes(8));
        $hash = hash('sha256', $salt.$value, true);
        $hashId = $this->base62Encode(substr($hash, 0, $length));
        // Ensure the hashId is exactly $length characters
        if (strlen($hashId) < $length) {
            $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            while (strlen($hashId) < $length) {
                $hashId .= $alphabet[random_int(0, 61)];
            }
        } elseif (strlen($hashId) > $length) {
            $hashId = substr($hashId, 0, $length);
        }

        return $hashId;
    }

    /**
     * Encode bytes to base62 string.
     */
    public function base62Encode(string $bytes): string
    {
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $num = gmp_import($bytes);
        $base62 = '';
        while (gmp_cmp($num, 0) > 0) {
            [$num, $rem] = [gmp_div_q($num, 62), gmp_mod($num, 62)];
            $base62 = $alphabet[gmp_intval($rem)].$base62;
        }

        return $base62 ?: '0';
    }

    /**
     * Validate that a value is a valid hashId.
     */
    public static function isValid(string $value, ?int $length = null): bool
    {
        if ($length !== null) {
            return (bool) preg_match('/^[0-9A-Za-z]{'.$length.'}$/', $value);
        }

        return (bool) preg_match('/^[0-9A-Za-z]+$/', $value);
    }

    /**
     * Always return the same hashId.
     *
     * @return string The frozen hashId value
     */
    public static function freeze(?Closure $callback = null): string
    {
        $frozenValue = (new self)->generate();

        static::createUsing(fn () => $frozenValue);

        if ($callback !== null) {
            try {
                $callback($frozenValue);
            } finally {
                static::createNormally();
            }
        }

        return $frozenValue;
    }

    /**
     * Set a custom factory for generating hashIds.
     */
    public static function createUsing(?callable $factory = null): void
    {
        static::$customFactory = $factory !== null ? $factory(...) : null;
    }

    /**
     * Generate hashIds from a predefined sequence.
     */
    public static function createUsingSequence(array $sequence, ?callable $whenMissing = null): void
    {
        static::$sequence = $sequence;
        static::$sequenceIndex = 0;
        static::$customFactory = $whenMissing !== null ? $whenMissing(...) : null;
    }

    /**
     * Reset hashId generation to normal behavior.
     */
    public static function createNormally(): void
    {
        static::$customFactory = null;
        static::$sequence = null;
        static::$sequenceIndex = 0;
    }

    /**
     * Get the next value from the sequence.
     */
    protected static function nextInSequence(): string
    {
        if (static::$sequenceIndex < count(static::$sequence)) {
            return static::$sequence[static::$sequenceIndex++];
        }

        // Sequence exhausted — fall back to custom factory or normal generation
        static::$sequence = null;

        if (static::$customFactory !== null) {
            return (static::$customFactory)(16);
        }

        return (new self)->generate();
    }
}
