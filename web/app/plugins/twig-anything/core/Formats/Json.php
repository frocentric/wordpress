<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\Formats;

class Json implements FormatInterface
{
    public function getSlug() {
        return 'json';
    }

    public function getVersion() {
        return '1';
    }

    public function getShortName() {
        return 'JSON';
    }

    public function getLongName() {
        return __('JSON-encoded string', 'twig-anything');
    }

    public function getDescription() {
        return __('Parse retrieved data as a JSON-encoded string.');
    }

    public function getDefaultConfig() {
        return array();
    }
    public function getUrlToComponentJs() {
        return '';
    }

    public function parseFromDataSource($config, $retrievedData) {
        $s = (string)$retrievedData;
        $data = json_decode($s, true);
        if (is_null($data)) {
            throw new ParseFromDataSourceException('Cannot json-decode the data retrieved. The json_decode() PHP function returned NULL.');
        }
        return $data;
    }

    public function serializeForCache($data) {
        // Parsed JSON data is always an array, so we can cache it as is
        return $data;
    }

    public function deserializeFromCache($cachedValue) {
        // Cached data is already an array, so we don't need to apply any conversion
        return $cachedValue;
    }
}