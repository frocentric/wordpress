<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\DataSources;

use TwigAnything\TwigAnythingException;

interface DataSourceInterface
{
    public function getSlug();
    public function getVersion();
    public function getShortName();
    public function getLongName();
    public function getDescription();
    public function getDefaultConfig();
    public function getNonHashedCacheKey($config);
    public function getUrlToComponentJs();
    public function fetchData($config);
}

class DataSourceException extends TwigAnythingException {};
class DataSourceSlugExistsException extends DataSourceException{};
class DataSourceConfigurationException extends DataSourceException{};
class ShortcodeConfigurationException extends DataSourceException{};
class DataRetrievalException extends DataSourceException{};
class DataSerializeException extends DataSourceException{};
class DataUnserializeException extends DataSourceException{};
class DisplayNothing extends DataSourceException{};