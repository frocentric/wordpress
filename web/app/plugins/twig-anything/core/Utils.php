<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

class Utils
{
    public static function arrayMergeDeepArrays($arrays) {
        $result = array();

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                // Renumber integer keys as array_merge_recursive() does. Note that PHP
                // automatically converts array keys that are integer strings (e.g., '1')
                // to integers.
                if (is_integer($key)) {
                    $result [] = $value;
                }
                // Recurse when both values are arrays.
                elseif (isset($result [$key]) && is_array($result [$key]) && is_array($value)) {
                    $result [$key] = static::arrayMergeDeepArrays(array($result [$key], $value));
                }
                // Otherwise, use the latter value, overriding any previous value.
                else {
                    $result [$key] = $value;
                }
            }
        }

        return $result;
    }

    public static function arrayMergeDeep() {
        $args = func_get_args();
        return self::arrayMergeDeepArrays($args);
    }
}