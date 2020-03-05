<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\DataSources;

use Exception;
use TwigAnything\TwigAnything;
use TwigAnything\TwigHelper;
use wpdb;

class MySQL implements DataSourceInterface
{
    public function getSlug() {
        return 'mysql';
    }

    public function getVersion() {
        return '3';
    }

    public function getShortName() {
        return __('MySQL');
    }

    public function getLongName() {
        return __('MySQL - run a MySQL query');
    }

    public function getDescription() {
        return __('Fetches data from the MySQL database of this WordPress instance by using WordPress\' native database API.', 'twig-anything');
    }

    public function getDefaultConfig() {
        return array(
            'mysql' => '',
            'mysql_type' => ''
        );
    }

    public function compileConfig($config) {
        # Build MySQL query
        try {
            $config['mysql'] = TwigHelper::renderTemplateInDefaultTwigEnvironment(
                $config['mysql'],
                $context = array(),
                $configOverride = array(
                    'isRemoveLineBreaksFromTemplate' => false
                )
            );
        }
        catch (Exception $e) {
            throw new DataSourceConfigurationException('Failed preparing MySQL query: '.$e->getMessage());
        }
        return $config;
    }

    public function getNonHashedCacheKey($config) {
        return $config['mysql_type'].$config['mysql'];
    }

    public function getUrlToComponentJs() {
        return plugins_url('jsx/data-sources/mysql.js', TwigAnything::pluginFile());
    }

    public function fetchData($config) {
        # Extract mysql script
        $mysql = $config['mysql'];
        if (empty($mysql)) {
            throw new DataSourceConfigurationException('MySQL Query not specified.');
        }

        # Extract mysql type
        $mysqlType = $config['mysql_type'];
        if (empty($mysqlType)) {
            throw new DataSourceConfigurationException('MySQL Result Type not specified.');
        }

        # Fetch data from the MySQL database

        /** @var wpdb $wpdb */
        global $wpdb;

        switch ($mysqlType) {
            case 'get_var':
                return $wpdb->get_var($mysql);
            case 'get_col':
                return $wpdb->get_col($mysql);
            case 'get_row':
                return $wpdb->get_row($mysql, ARRAY_A);
            default: # 'get_results' and unrecognized types
                return $wpdb->get_results($mysql, ARRAY_A);
        }
    }
}