<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

class Cache
{
    const TRANSIENT_PREFIX = 'TwigAnCCH';

    private $transientKey;
    private $isEmpty = true;
    private $isBroken = false;
    private $version = null;
    private $timestamp = null;
    private $lifetime = null;
    private $value = null;

    public function __construct($transientKey, $transientData) {
        $this->transientKey = $transientKey;
        if ($transientData === false) {
            return;
        }
        if (!is_array($transientData)) {
            $this->isBroken = true;
            return;
        }
        foreach(array('version', 'timestamp', 'lifetime', 'value') as $key) {
            if (!array_key_exists($key, $transientData)) {
                $this->isBroken = true;
                return;
            }
        }

        $version = $transientData['version'];
        if (!is_scalar($version) || !ctype_digit((string)$version)) {
            $this->isBroken = true;
            return;
        }

        $timestamp = $transientData['timestamp'];
        if (!is_scalar($timestamp) || !ctype_digit((string)$timestamp)) {
            $this->isBroken = true;
            return;
        }

        $lifetime = $transientData['lifetime'];
        if ($lifetime === '') {
            $lifetime = 0;
        }
        if (!is_scalar($lifetime) || !ctype_digit((string)$lifetime)) {
            $this->isBroken = true;
            return;
        }

        $this->isEmpty = false;
        $this->version = $version;
        $this->timestamp = $timestamp;
        $this->lifetime = $lifetime;
        $this->value = $transientData['value'];
    }

    /**
     * Gets the transient key used to store the cache value.
     * Maximum possible length is 40
     * (see https://core.trac.wordpress.org/ticket/15058)
     *
     * @return mixed
     */
    public function getTransientKey() {
        return $this->transientKey;
    }

    /**
     * Gets the version of the cache structure.
     *
     * @return int
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Gets the cache unix timestamp, i.e. when the cache was saved.
     *
     * @return mixed
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * Gets the cache lifetime in seconds.
     * 0 represents no expiration lifetime.
     *
     * @return int
     */
    public function getLifetime() {
        return $this->lifetime;
    }

    /**
     * Gets the cached value.
     *
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Returns TRUE if the transient data passed to the constructor
     * was a boolean FALSE or broken. If cached value is present,
     * always return FALSE, even if the data has been expired.
     * To check for expiration, use @see isExpired().
     *
     * @return bool
     */
    public function isEmpty() {
        return $this->isEmpty;
    }

    /**
     * Returns TRUE if the transient data passed to the constructor
     * was not boolean FALSE nor broken. Returns TRUE even if data has been
     * expired. To check for expiration, use @see isExpired().
     *
     * @return bool
     */
    public function hasValue() {
        return !$this->isEmpty;
    }

    /**
     * Returns TRUE if the transient data passed to the constructor
     * was broken, i.e. did not represent a correct cache structure.
     * This also means that isEmpty() will return TRUE.
     *
     * @return bool
     */
    public function isBroken() {
        return $this->isBroken;
    }

    /**
     * Returns TRUE if the cached value is out of date.
     *
     * @return bool
     */
    public function isExpired() {
        return $this->hasValue()
            && !empty($this->lifetime)
            && ($this->lifetime + $this->timestamp < time());
    }

    /**
     * Given a non-hashed cache key of any length, builds a transient key
     * consisting of exactly 40 characters length, which is the maximum
     * for set_transient() and set_site_transient().
     *
     * @param $nonHashedCacheKey
     * @return string
     */
    private static function buildTransientKey($nonHashedCacheKey) {
        $key = sha1($nonHashedCacheKey); # 40 chars
        $key = base_convert($key, 16, 36); # 31 chars
        $key = self::TRANSIENT_PREFIX . $key; # 40 chars = 9 + 31

        return $key;
    }

    /**
     * Saves the value into cache by the given transient key
     * and for the given lifetime. An instance of Cache is returned.
     *
     * @param string $transientKey
     * @param int $lifetime
     * @param mixed $value
     * @return Cache
     */
    private static function save($transientKey, $lifetime, $value) {
        if (empty($lifetime)) {
            $lifetime = 0;
        }
        $data = array(
            'version' => 1,
            'timestamp' => time(),
            'lifetime' => (int) $lifetime,
            'value' => $value
        );

        # We don't want the transient to expire. However, if we set expiration
        # to 0, the transient will autoload with every page in WordPress.
        # To simulate a never-expiring transient, we set expiration time
        # to 10 years.
        $res = set_transient($transientKey, $data, 10 * YEAR_IN_SECONDS);
        if ($res === false) {
            throw new CacheSaveException('An error has occurred while saving a transient by key: '.$transientKey);
        }

        return new Cache($transientKey, $data);
    }

    /**
     * Loads a value from cache by the given transient key.
     * The result is always an instance of Cache, even if there is no data
     * in cache found by the transient key.
     *
     * @param $transientKey
     * @return Cache
     */
    private static function load($transientKey) {
        $data = get_transient($transientKey);
        return new Cache($transientKey, $data);
    }

    private static function buildDataSourceTransientKey($slug, $nonHashedCacheKey) {
        return self::buildTransientKey($slug.'|~|'.$nonHashedCacheKey);
    }

    /**
     * Saves the data source fetching result to cache.
     *
     * @param string $slug
     * @param string $nonHashedCacheKey
     * @param int $lifetime
     * @param mixed $data
     * @return Cache
     */
    public static function saveDataSourceFetched($slug, $nonHashedCacheKey, $lifetime, $data) {
        $key = self::buildDataSourceTransientKey($slug, $nonHashedCacheKey);
        return self::save($key, $lifetime, $data);
    }

    /**
     * Loads the data source fetching result from cache.
     *
     * @param string $slug
     * @param string $nonHashedCacheKey
     * @return Cache
     */
    public static function loadDataSourceFetched($slug, $nonHashedCacheKey) {
        $key = self::buildDataSourceTransientKey($slug, $nonHashedCacheKey);
        return self::load($key);
    }
}

class CacheException extends TwigAnythingException{};
class CacheSaveException extends CacheException{};