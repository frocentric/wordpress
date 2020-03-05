<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\DataSources;

use Exception;
use TwigAnything\TwigAnything;
use TwigAnything\TwigHelper;

class Url implements DataSourceInterface
{
    public function getSlug() {
        return 'url';
    }

    public function getVersion() {
        return '2';
    }

    public function getShortName() {
        return __('URL', 'twig-anything');
    }

    public function getLongName() {
        return __('URL - fetch from URL by an HTTP request', 'twig-anything');
    }

    public function getDescription() {
        return __('Fetches data from URL by an HTTP GET/POST request.', 'twig-anything');
    }

    public function getDefaultConfig() {
        return array(
            'url'    => '',
            'method' => 'POST'
        );
    }

    public function compileConfig($config) {
        # URL is a Twig Template
        try {
            $config['url'] = TwigHelper::renderTemplateInDefaultTwigEnvironment($config['url']);
        }
        catch (Exception $e) {
            throw new DataSourceConfigurationException('Failed compiling URL with Twig syntax: '.$e->getMessage());
        }
        return $config;
    }

    public function getNonHashedCacheKey($config) {
        return $config['method'] . ' ' . $config['url'];
    }

    public function getUrlToComponentJs() {
        return plugins_url('jsx/data-sources/url.js', TwigAnything::pluginFile());
    }

    public function fetchData($config) {
        // For testing purposes, uncomment:
        // throw new DataRetrievalException('Data retrieval test exception.');

        # Extract URL attribute
        $url = $config['url'];
        if (empty($url)) {
            throw new DataSourceConfigurationException('Data URL not specified or empty.');
        }

        # Extract HTTP METHOD to use
        $method = strtoupper(trim($config['method']));
        if (empty($method)) {
            throw new DataSourceConfigurationException('Data URL Method not specified or empty.');
        }

        # Fetch data from URL
        switch ($method) {
            case 'GET':
                $response = wp_remote_get($url);
                break;
            case 'POST':
                $response = wp_remote_post($url);
                break;
            default:
                throw new DataSourceConfigurationException('Unknown Data URL Method, expected GET or POST.');
        }

        if (is_wp_error($response)) {
            throw new DataRetrievalException($response->get_error_message());
        }
        if (!is_array($response) || !isset($response['body'])) {
            $data = '';
        }
        else {
            $data = $response['body'];
        }
        return $data;
    }
}