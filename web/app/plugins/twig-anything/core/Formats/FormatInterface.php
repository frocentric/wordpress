<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\Formats;

use TwigAnything\TwigAnythingException;

interface FormatInterface
{
    public function getSlug();
    public function getVersion();
    public function getShortName();
    public function getLongName();
    public function getDescription();
    public function getDefaultConfig();
    public function getUrlToComponentJs();
    public function parseFromDataSource($config, $retrievedData);
    public function serializeForCache($data);
    public function deserializeFromCache($cachedValue);
}

class FormatException extends TwigAnythingException {};
class FormatConfigurationException extends FormatException {};
class FormatSlugExistsException extends FormatException {};
class ParseFromDataSourceException extends FormatException {};
class SerializeForCacheException extends FormatException {};
class DeserializeFromCacheException extends FormatException {};