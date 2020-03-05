<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\Formats;

use LibXMLError;

class XML implements FormatInterface
{
    public function getSlug() {
        return 'xml';
    }

    public function getVersion() {
        return '1';
    }

    public function getShortName() {
        return 'XML';
    }

    public function getLongName() {
        return __('XML string');
    }

    public function getDescription() {
        return __('Parse retrieved data as an XML string.');
    }

    public function getDefaultConfig() {
        return array();
    }
    public function getUrlToComponentJs() {
        return '';
    }

    public function parseFromDataSource($config, $retrievedData) {
        if ($retrievedData === null || $retrievedData === false || $retrievedData === '') {
            return $retrievedData;
        }
        
        $xmlString = (string) $retrievedData;

        $prevUseErrors = libxml_use_internal_errors(true);
        $data = simplexml_load_string($xmlString);
        if ($data !== false) {
            libxml_use_internal_errors($prevUseErrors);
            return $data;
        }

        # Extract the first error message
        $firstXmlError = '';
        /** @var LibXMLError $xmlError */
        foreach (libxml_get_errors() as $xmlError) {
            $firstXmlError = $xmlError->code . ': '.$xmlError->message;
            break;
        }
        libxml_use_internal_errors($prevUseErrors);
        throw new ParseFromDataSourceException('XML parsing error: '.$firstXmlError);
    }

    public function serializeForCache($data) {
        if ($data === null || $data === false || $data === '') {
            return $data;
        }
        # Convert XML object to string for caching
        if (!($data instanceof \SimpleXMLElement)) {
            throw new SerializeForCacheException('Cannot cache "'.$this->getSlug().'" data source because it is not represented by an instance of SimpleXMLElement object.');
        }
        return $data->asXML();
    }

    public function deserializeFromCache($cachedValue) {
        try {
            $data = $this->parseFromDataSource(array(), $cachedValue);
            return $data;
        }
        catch (ParseFromDataSourceException $e) {
            throw new DeserializeFromCacheException($e->getMessage(), $e->getCode());
        }
    }
}