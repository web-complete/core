<?php

namespace WebComplete\core\utils\helpers;

/**
 * Class InflectorHelper
 * based on Yii BaseInflector
 */
class InflectorHelper
{

    /**
     * Converts an underscored or CamelCase word into a English
     * sentence.
     * @param string $words
     * @param bool $ucAll whether to set all words to uppercase
     * @return string
     */
    public function titleize($words, $ucAll = false): string
    {
        $words = $this->humanize($this->underscore($words), $ucAll);

        return $ucAll ? \ucwords($words) : \ucfirst($words);
    }

    /**
     * Returns given word as CamelCased
     * Converts a word like "send_email" to "SendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "WhoSOnline"
     * @see variablize()
     * @param string $word the word to CamelCase
     * @return string
     */
    public function camelize($word): string
    {
        return \str_replace(' ', '', \ucwords(
            \preg_replace('/[^A-Za-z0-9]+/', ' ', $word)
        ));
    }

    /**
     * Converts a CamelCase name into space-separated words.
     * For example, 'PostTag' will be converted to 'Post Tag'.
     * @param string $name the string to be converted
     * @param bool $ucwords whether to capitalize the first letter in each word
     * @return string the resulting words
     */
    public function camel2words($name, $ucwords = true): string
    {
        $label = \strtolower(\trim(\str_replace([
            '-',
            '_',
            '.',
        ], ' ', \preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $name))));

        return $ucwords ? \ucwords($label) : $label;
    }

    /**
     * Converts a CamelCase name into an ID in lowercase.
     * Words in the ID may be concatenated using the specified character (defaults to '-').
     * For example, 'PostTag' will be converted to 'post-tag'.
     * @param string $name the string to be converted
     * @param string $separator the character used to concatenate the words in the ID
     * @param bool|string $strict whether to insert a separator between two consecutive uppercase chars
     * @return string the resulting ID
     */
    public function camel2id($name, $separator = '-', $strict = false): string
    {
        $regex = $strict ? '/[A-Z]/' : '/(?<![A-Z])[A-Z]/';
        if ($separator === '_') {
            return \strtolower(\trim(\preg_replace($regex, '_\0', $name), '_'));
        }
        return \strtolower(\trim(
            \str_replace('_', $separator, \preg_replace($regex, $separator . '\0', $name)),
            $separator
        ));
    }

    /**
     * Converts an ID into a CamelCase name.
     * Words in the ID separated by `$separator` (defaults to '-') will be concatenated into a CamelCase name.
     * For example, 'post-tag' is converted to 'PostTag'.
     * @param string $id the ID to be converted
     * @param string $separator the character used to separate the words in the ID
     * @return string the resulting CamelCase name
     */
    public function id2camel($id, $separator = '-'): string
    {
        return \str_replace(' ', '', \ucwords(\implode(' ', \explode($separator, $id))));
    }

    /**
     * Converts any "CamelCased" into an "underscored_word".
     * @param string $words the word(s) to underscore
     * @return string
     */
    public function underscore($words): string
    {
        return \strtolower(\preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $words));
    }

    /**
     * Returns a human-readable string from $word
     * @param string $word the string to humanize
     * @param bool $ucAll whether to set all words to uppercase or not
     * @return string
     */
    public function humanize($word, $ucAll = false): string
    {
        $word = \str_replace('_', ' ', \preg_replace('/_id$/', '', $word));

        return $ucAll ? \ucwords($word) : \ucfirst($word);
    }

    /**
     * Same as camelize but first char is in lowercase.
     * Converts a word like "send_email" to "sendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "whoSOnline"
     * @param string $word to lowerCamelCase
     * @return string
     */
    public function variablize($word): string
    {
        $word = $this->camelize($word);

        return \strtolower($word[0]) . \substr($word, 1);
    }
}
