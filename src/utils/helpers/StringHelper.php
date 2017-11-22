<?php

namespace WebComplete\core\utils\helpers;

class StringHelper
{
    /**
     * Encodes string into "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648)
     *
     * > Note: Base 64 padding `=` may be at the end of the returned string.
     * > `=` is not transparent to URL encoding.
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input the string to encode.
     * @return string encoded string.
     */
    public function base64UrlEncode($input): string
    {
        return \strtr(\base64_encode($input), '+/', '-_');
    }

    /**
     * Decodes "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648)
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input encoded string.
     * @return string decoded string.
     */
    public function base64UrlDecode($input): string
    {
        return \base64_decode(\strtr($input, '-_', '+/'));
    }

    /**
     * Returns the number of bytes in the given string.
     * This method ensures the string is treated as a byte array by using `mb_strlen()`.
     * @param string $string the string being measured for length
     * @return int the number of bytes in the given string.
     */
    public function byteLength($string)
    {
        return \mb_strlen($string, '8bit');
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     * This method ensures the string is treated as a byte array by using `mb_substr()`.
     * @param string $string the input string. Must be one character or longer.
     * @param int $start the starting position
     * @param int $length the desired portion length. If not specified or `null`, there will be
     * no limit on length i.e. the output will be until the end of the string.
     * @return string the extracted part of string, or FALSE on failure or an empty string.
     * @see http://www.php.net/manual/en/function.substr.php
     */
    public function byteSubstr($string, $start, $length = null)
    {
        return \mb_substr($string, $start, $length === null ? \mb_strlen($string, '8bit') : $length, '8bit');
    }
}
