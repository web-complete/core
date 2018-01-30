<?php

namespace WebComplete\core\utils\helpers;

class ArrayHelper
{

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays. So it is better to be done specifying an array of key names
     * like `['x', 'y', 'z']`.
     *
     * Below are some usage examples,
     *
     * ```php
     * // working with array
     * $username = \yii\helpers\ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = \yii\helpers\ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = \yii\helpers\ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = \yii\helpers\ArrayHelper::getValue($users, 'address.street');
     * // using an array of keys to retrieve the value
     * $value = \yii\helpers\ArrayHelper::getValue($versions, ['1.0', 'date']);
     * ```
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure|array $key key name of the array element, an array of keys or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * The possibility to pass an array of keys is available since version 2.0.4.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     */
    public function getValue(array $array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (\is_array($key)) {
            $lastKey = \array_pop($key);
            foreach ((array)$key as $keyPart) {
                $array = $this->getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (\is_array($array) && (isset($array[$key]) || \array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = \strrpos($key, '.')) !== false) {
            $array = $this->getValue($array, \substr($key, 0, $pos), $default);
            $key = (string)\substr($key, $pos + 1);
        }

        if (\is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        }

        if (\is_array($array)) {
            return (isset($array[$key]) || \array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }

    /**
     * Writes a value into an associative array at the key path specified.
     * If there is no such key path yet, it will be created recursively.
     * If the key exists, it will be overwritten.
     *
     * ```php
     *  $array = [
     *      'key' => [
     *          'in' => [
     *              'val1',
     *              'key' => 'val'
     *          ]
     *      ]
     *  ];
     * ```
     *
     * The result of `ArrayHelper::setValue($array, 'key.in.0', ['arr' => 'val']);` will be the following:
     *
     * ```php
     *  [
     *      'key' => [
     *          'in' => [
     *              ['arr' => 'val'],
     *              'key' => 'val'
     *          ]
     *      ]
     *  ]
     *
     * ```
     *
     * The result of
     * `ArrayHelper::setValue($array, 'key.in', ['arr' => 'val']);` or
     * `ArrayHelper::setValue($array, ['key', 'in'], ['arr' => 'val']);`
     * will be the following:
     *
     * ```php
     *  [
     *      'key' => [
     *          'in' => [
     *              'arr' => 'val'
     *          ]
     *      ]
     *  ]
     * ```
     *
     * @param array $array the array to write the value to
     * @param string|array|null $path the path of where do you want to write a value to `$array`
     * the path can be described by a string when each key should be separated by a dot
     * you can also describe the path as an array of keys
     * if the path is null then `$array` will be assigned the `$value`
     * @param mixed $value the value to be written
     * @since 2.0.13
     */
    public function setValue(array &$array, $path, $value)
    {
        if ($path === null) {
            $array = $value;
            return;
        }

        $keys = \is_array($path) ? $path : \explode('.', $path);

        while (\count($keys) > 1) {
            $key = \array_shift($keys);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            if (!\is_array($array[$key])) {
                $array[$key] = [$array[$key]];
            }
            $array = &$array[$key];
        }

        $array[\array_shift($keys)] = $value;
    }

    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $array1 array to be merged to
     * @param array $array2 array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public function merge(array $array1, array $array2)
    {
        $args = \func_get_args();
        $result = \array_shift($args);
        while (!empty($args)) {
            $next = \array_shift($args);
            foreach ((array)$next as $k => $v) {
                if (\is_int($k)) {
                    if (\array_key_exists($k, $result)) {
                        $result[] = $v;
                    } else {
                        $result[$k] = $v;
                    }
                } elseif (\is_array($v) && isset($result[$k]) && \is_array($result[$k])) {
                    $result[$k] = $this->merge($result[$k], $v);
                } else {
                    $result[$k] = $v;
                }
            }
        }

        return $result;
    }
}
