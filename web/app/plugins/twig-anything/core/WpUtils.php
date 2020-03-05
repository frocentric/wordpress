<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WpUtils
{
    public static function jsonEncodeForHtml($var) {
        return json_encode($var, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
    }

    public static function postVar($name, $default = null) {
        return array_key_exists($name, $_POST)? $_POST[$name] : $default;
    }
}