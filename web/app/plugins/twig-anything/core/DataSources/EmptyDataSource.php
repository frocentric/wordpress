<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\DataSources;

class EmptyDataSource implements DataSourceInterface
{
    public function getSlug() {
        return 'empty';
    }

    public function getVersion() {
        return '1';
    }

    public function getShortName() {
        return __('Empty', 'twig-anything');
    }

    public function getLongName() {
        return __('Empty - do not fetch any data', 'twig-anything');
    }

    public function getDescription() {
        return __('Always returns empty data. Useful for template combinations and testing.', 'twig-anything');
    }

    public function getDefaultConfig() {
        return array();
    }

    public function getNonHashedCacheKey($config) {
        return '';
    }

    public function getUrlToComponentJs() {
        return '';
    }

    public function fetchData($config) {
        return null;
    }
}