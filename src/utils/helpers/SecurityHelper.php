<?php

namespace WebComplete\core\utils\helpers;

class SecurityHelper
{

    /**
     * @var StringHelper
     */
    protected $stringHelper;

    public function __construct(StringHelper $stringHelper)
    {
        $this->stringHelper = $stringHelper;
    }

    /**
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public function cryptPassword(string $password, string $salt): string
    {
        return \strtoupper(\md5(\md5($salt) . \md5($password)));
    }

    /**
     * Masks a token to make it uncompressible.
     * Applies a random mask to the token and prepends the mask used to the result making the string always unique.
     * Used to mitigate BREACH attack by randomizing how token is outputted on each request.
     *
     * @param string $token An unmasked token.
     *
     * @return string A masked token.
     * @throws \RuntimeException
     */
    public function maskToken($token): string
    {
        // The number of bytes in a mask is always equal to the number of bytes in a token.
        $mask = $this->generateRandomKey($this->stringHelper->byteLength($token));
        return $this->stringHelper->base64UrlEncode($mask . ($mask ^ $token));
    }

    /**
     * Unmasks a token previously masked by `maskToken`.
     * @param string $maskedToken A masked token.
     * @return string An unmasked token, or an empty string in case of token format is invalid.
     */
    public function unmaskToken($maskedToken): string
    {
        $decoded = $this->stringHelper->base64UrlDecode($maskedToken);
        $length = $this->stringHelper->byteLength($decoded) / 2;
        // Check if the masked token has an even length.
        if (!is_int($length)) {
            return '';
        }
        return $this->stringHelper->byteSubstr($decoded, $length, $length) ^
            $this->stringHelper->byteSubstr($decoded, 0, $length);
    }

    /**
     * Generates specified number of random bytes.
     * Note that output may not be ASCII.
     *
     * @see generateRandomString() if you need a string.
     *
     * @param int $length the number of bytes to generate
     *
     * @return string the generated random bytes
     * @throws \RuntimeException
     */
    public function generateRandomKey(int $length = 32): string
    {
        if ($length < 1) {
            throw new \RuntimeException('First parameter ($length) must be greater than 0');
        }

        // always use random_bytes() if it is available
        if (\function_exists('random_bytes')) {
            return \random_bytes($length);
        }
        throw new \RuntimeException('Function random_bytes not found');
    }

    /**
     * Generates a random string of specified length.
     * The string generated matches [A-Za-z0-9_-]+ and is transparent to URL-encoding.
     *
     * @param int $length the length of the key in characters
     *
     * @return string the generated random key
     * @throws \RuntimeException
     */
    public function generateRandomString(int $length = 32): string
    {
        if ($length < 1) {
            throw new \RuntimeException('First parameter ($length) must be greater than 0');
        }

        $bytes = $this->generateRandomKey($length);
        return \substr($this->stringHelper->base64UrlEncode($bytes), 0, $length);
    }
}
