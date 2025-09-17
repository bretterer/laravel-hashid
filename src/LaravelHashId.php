<?php

namespace Bretterer\LaravelHashId;

class LaravelHashId
{
    /**
     * Generate a collision-resistant hashId.
     *
     * @param  int  $length  Length of the hashId (default: 16)
     */
    public function generate(int $length = 16): string
    {
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
}
