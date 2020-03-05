<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\Formats;

class Raw implements FormatInterface
{
    public function getSlug() {
        return 'raw';
    }

    public function getVersion() {
        return '1';
    }

    public function getShortName() {
        return 'Raw data';
    }

    public function getLongName() {
        return 'Raw data - use retrieved data as is';
    }

    public function getDescription() {
        return 'Use raw retrieved data as is and do not apply any transformation after it is retrieved.';
    }

    public function getDefaultConfig() {
        return array();
    }
    public function getUrlToComponentJs() {
        return '';
    }

    public function parseFromDataSource($config, $retrievedData) {
        return $retrievedData;
    }

    public function serializeForCache($data) {
        return $data;
    }

    public function deserializeFromCache($cachedValue) {
        return $cachedValue;
    }
}