<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

use Twig_Error;
use TwigAnything\DataSources\DataSourceException;
use wpdb;

class TwigTemplate
{
    /**
     * @var TwigAnything
     */
    private $twigAnything;

    private $id;
    private $content;
    private $config;

    public function __construct($twigAnything, $id, $content, $config) {
        $this->twigAnything = $twigAnything;
        $this->id = $id;
        $this->content = $content;
        if (empty($config) || !is_array($config)) {
            $config = array();
        }
        $this->config = $config;
    }

    /**
     * @return TwigAnything
     */
    public function getTwigAnything() {
        return $this->twigAnything;
    }

    public function getId() {
        return $this->id;
    }

    public function getContent() {
        return $this->content;
    }

    public function getConfig() {
        return $this->config;
    }

    /**
     * @param array $row
     * @return self
     */
    private static function newFromRow($row) {
        $id = $row['ID'];
        $content = $row['post_content'];
        $config = PostMetaBoxes::loadDataSourceMetaBoxSettings($id);

        return new self(twigAnything(), $id, $content, $config);
    }

    /**
     * @param string $queryString
     * @param string $notFoundMessage
     * @return self
     */
    private static function loadByQuery($queryString, $notFoundMessage) {
        /** @var wpdb $wpdb */
        global $wpdb;
        $row = $wpdb->get_row($queryString, ARRAY_A);
        if (empty($row)) {
            $error = $notFoundMessage;
            if ($wpdb->last_error) {
                $error .= " MySql error: ".$wpdb->last_error;
            }
            throw new TwigTemplateLoadException($error);
        }
        return self::newFromRow($row);
    }

    /**
     * @param int $postId
     * @return self
     */
    public static function loadById($postId) {
        /** @var wpdb $wpdb */
        global $wpdb;
        $sqlQuery = $wpdb->prepare("
            SELECT ID, post_content
            FROM $wpdb->posts
            WHERE ID = %d
            LIMIT 1
            ", $postId);
        return self::loadByQuery($sqlQuery, "Cannot find a Twig Template by the given ID.");
    }

    /**
     * @param string $slug
     * @return self
     */
    public static function loadPublishedBySlug($slug) {
        /** @var wpdb $wpdb */
        global $wpdb;
        $sqlQuery = $wpdb->prepare("
            SELECT ID, post_content
            FROM $wpdb->posts
            WHERE post_type = %s
              AND post_status = 'publish'
              AND post_name = %s
            LIMIT 1
            ", TwigAnything::POST_TYPE, $slug);
        return self::loadByQuery($sqlQuery, "Cannot find a published Twig Template by the given slug.");
    }

    /**
     * @param array $configOverride
     * @return string
     *
     * @throws TwigTemplateConfigurationException
     * @throws DataSourceException
     */
    public function render($configOverride = array()) {
        $config = Utils::arrayMergeDeep($this->config, $configOverride);
        $commonSettings = $config['commonSettings'];

        $dataSourcesRegister = $this->twigAnything->getDataSources();
        $formatsRegister = $this->twigAnything->getFormats();

        # Get source type
        $sourceType = array_key_exists('source_type', $commonSettings)? $commonSettings['source_type'] : null;
        $dataSource = $dataSourcesRegister->getBySlug($sourceType);
        if (empty($dataSource)) {
            throw new TwigTemplateConfigurationException("Data source not found by slug \"$sourceType\".");
        }

        # Get data source settings
        $dataSourceSettings = array_key_exists('dataSourceSettings', $config)? $config['dataSourceSettings']: array();
        $dataSourceSettings = Utils::arrayMergeDeep($dataSource->getDefaultConfig(), $dataSourceSettings);

        if (method_exists($dataSource, 'compileConfig')) {
            $compiledDataSourceSettings = $dataSource->compileConfig($dataSourceSettings);
        }
        else {
            $compiledDataSourceSettings = $dataSourceSettings;
        }

        # Get format
        $formatSlug = array_key_exists('format', $commonSettings)? $commonSettings['format'] : null;
        $format = $formatsRegister->getBySlug($formatSlug);
        if (empty($format)) {
            throw new TwigTemplateConfigurationException("Format not found by slug \"$formatSlug\".");
        }

        # Get format settings
        $formatSettings = array_key_exists('formatSettings', $config)? $config['formatSettings']: array();
        $formatSettings = Utils::arrayMergeDeep($format->getDefaultConfig(), $formatSettings);

        # Get cache lifetime
        $cacheSeconds = array_key_exists('cache_seconds', $commonSettings)? $commonSettings['cache_seconds'] : null;

        # Get data handling configuration
        $onDataError = array_key_exists('on_data_error', $commonSettings)? $commonSettings['on_data_error'] : null;

        # Retrieve $data to pass to Twig. The logic is quite complex,
        # but it is to make sure there is no unneeded data fetching and
        # cache loading

        $data = null;
        /** @var Cache $cache */
        $cache = null;
        $useCache = !empty($cacheSeconds);
        $useCacheFallback = in_array($onDataError, array('use_cache_or_display_nothing', 'use_cache_or_display_error'));

        $fFetchData = function()use($dataSource, $compiledDataSourceSettings, $formatSettings, $format) {
            $retrievedData = $dataSource->fetchData($compiledDataSourceSettings);
            $parsed = $format->parseFromDataSource($formatSettings, $retrievedData);
            return $parsed;
        };

        $fLoadCache = function()use($dataSource, $compiledDataSourceSettings) {
            return Cache::loadDataSourceFetched(
                $dataSource->getSlug(),
                $dataSource->getNonHashedCacheKey($compiledDataSourceSettings)
            );
        };

        $fDeserializeCache = function(Cache $cache)use($format) {
            return $format->deserializeFromCache($cache->getValue());
        };

        $fSaveCache = function($data)use($dataSource, $compiledDataSourceSettings, $cacheSeconds, $format) {
            $slug = $dataSource->getSlug();
            $key = $dataSource->getNonHashedCacheKey($compiledDataSourceSettings);
            $serialized = $format->serializeForCache($data);
            Cache::saveDataSourceFetched($slug, $key, $cacheSeconds, $serialized);
        };

        $fDisplayErrorOrEmpty = function(\Exception $e = null)use($onDataError) {
            if ($onDataError == 'use_cache_or_display_nothing') {
                return '';
            }
            throw $e;
        };

        # Use cache
        if ($useCache) {
            $cache = $fLoadCache();
            # Cache does not exist
            if ($cache->isEmpty()) {
                # Load data
                try {
                    $data = $fFetchData();
                    # Data load success
                    $fSaveCache($data);
                }
                catch (DataSourceException $e) {
                    # Data load failure
                    return $fDisplayErrorOrEmpty($e);
                }
            }
            # Cache exist
            else {
                # Cache has been expired
                if ($cache->isExpired()) {
                    # Load data
                    try {
                        $data = $fFetchData();
                        # Data load success
                        $fSaveCache($data);
                    }
                    catch (DataSourceException $e) {
                        # Data load failure
                        if ($useCacheFallback) {
                            # Use cache fallback
                            $data = $fDeserializeCache($cache);
                        }
                        else {
                            # Do not use cache fallback
                            return $fDisplayErrorOrEmpty($e);
                        }
                    }
                }
                # Cache has not been expired
                else {
                    $data = $fDeserializeCache($cache);
                }
            }
        }

        # Do not use cache
        else {
            # Load data
            try {
                $data = $fFetchData();
                # Data load success; do not store cache
            }
            catch (DataSourceException $e) {
                # Data load failure
                if ($useCacheFallback) {
                    # Use cache fallback
                    $cache = $fLoadCache();
                    if ($cache->hasValue()) {
                        # Cache exists
                        $data = $fDeserializeCache($cache);
                    }
                    else {
                        # Cache does not exist
                        return $fDisplayErrorOrEmpty($e);
                    }
                }
                else {
                    # Do not use cache fallback
                    return $fDisplayErrorOrEmpty($e);
                }
            }
        }

        # At this point we have some $data to pass to the Twig template

        # By default, we want to remove line breaks from the template
        $defaultIsRemoveLineBreaks = true;

        # This can be override in the $configOverride parameter
        if (array_key_exists('isRemoveLineBreaksFromTemplate', $config)) {
            $defaultIsRemoveLineBreaks = $config['isRemoveLineBreaksFromTemplate'];
        }

        # ... and by a filter as well
        $removeLineBreaksFromTemplate = apply_filters(
            'twig-anything-filter-is-remove-line-breaks-from-template',
            $defaultIsRemoveLineBreaks,
            $this
        );

        # Prepare template code
        if ($removeLineBreaksFromTemplate) {
            $normalizedTemplate = preg_replace('/[\\r\\n\\t]+/is', '', $this->content);
        }
        else {
            $normalizedTemplate = $this->content;
        }

        if (array_key_exists('twigConfigOverride', $config)) {
            $twigConfigOverride = $config['twigConfigOverride'];
        }
        else {
            $twigConfigOverride = array();
        }

        # Render Twig template
        $twigOutput = null;
        try {
            $twigOutput = TwigHelper::renderTwigAnythingShortcode($normalizedTemplate, $data, $twigConfigOverride);
        }
        catch (Twig_Error $e) {
            return 'Rendering Twig Template failed with the following message: '.$e->getMessage();
        }

        # Process all internal shortcodes
        $finalOutput = do_shortcode($twigOutput);

        return $finalOutput;
    }
}

class TwigTemplateException extends TwigAnythingException {};
class TwigTemplateLoadException extends TwigTemplateException {};
class TwigTemplateConfigurationException extends TwigTemplateException {};