<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\DataSources;

use TwigAnything\TwigAnything;

class File implements DataSourceInterface
{
    public function getSlug() {
        return 'file';
    }

    public function getVersion() {
        return '1';
    }

    public function getShortName() {
        return __('File', 'twig-anything');
    }

    public function getLongName() {
        return __('File - load from a file on your server', 'twig-anything');
    }

    public function getDescription() {
        return __('Loads data from a local file by either a full path or a path relative to the WordPress root path.', 'twig-anything');
    }

    public function getDefaultConfig() {
        return array(
            'file_path' => '',
            'is_file_path_absolute' => false
        );
    }

    private static function getFullPath($config) {
        if ($config['is_file_path_absolute']) {
            return $config['file_path'];
        }
        else {
            return ABSPATH . $config['file_path'];
        }
    }

    public function getNonHashedCacheKey($config) {
        return self::getFullPath($config);
    }

    public function getUrlToComponentJs() {
        return plugins_url('jsx/data-sources/file.js', TwigAnything::pluginFile());
    }

    public function fetchData($config) {
        $path = self::getFullPath($config);

        # Validate file path
        $filePath = $config['file_path'];
        if (empty($filePath)) {
            throw new DataSourceConfigurationException('File Path not specified.');
        }

        if (!file_exists($path)) {
            throw new DataRetrievalException("Cannot find file by its full path: ".$path);
        }
        if (!is_file($path) || is_dir($path)) {
            throw new DataRetrievalException('Full path is not a file: '.$path);
        }
        if (!is_readable($path)) {
            throw new DataRetrievalException("The file is not readable by its full path: ".$path);
        }

        # Fetch data from the file
        $data = file_get_contents($path);
        if ($data === false) {
            throw new DataRetrievalException('An error has occurred while reading file. We attempted to use the file_get_contents() PHP function, and it returned FALSE. File path: '.$path);
        }

        return $data;
    }
}