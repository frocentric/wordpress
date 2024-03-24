<?php

/**
 * Copyright 2020 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1;

use \ArrayAccess;
use \Amazon\ProductAdvertisingAPI\v1\ObjectSerializer;

/**
 * OfferMerchantInfo Class Doc Comment
 *
 * @category Class
 * @package  Amazon\ProductAdvertisingAPI\v1
 * @author   Product Advertising API team
 */
class OfferMerchantInfo implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'OfferMerchantInfo';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'defaultShippingCountry' => 'string',
        'feedbackCount' => 'int',
        'feedbackRating' => 'float',
        'id' => 'string',
        'name' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'defaultShippingCountry' => null,
        'feedbackCount' => 'int32',
        'feedbackRating' => null,
        'id' => null,
        'name' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'defaultShippingCountry' => 'DefaultShippingCountry',
        'feedbackCount' => 'FeedbackCount',
        'feedbackRating' => 'FeedbackRating',
        'id' => 'Id',
        'name' => 'Name'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'defaultShippingCountry' => 'setDefaultShippingCountry',
        'feedbackCount' => 'setFeedbackCount',
        'feedbackRating' => 'setFeedbackRating',
        'id' => 'setId',
        'name' => 'setName'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'defaultShippingCountry' => 'getDefaultShippingCountry',
        'feedbackCount' => 'getFeedbackCount',
        'feedbackRating' => 'getFeedbackRating',
        'id' => 'getId',
        'name' => 'getName'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['defaultShippingCountry'] = isset($data['defaultShippingCountry']) ? $data['defaultShippingCountry'] : null;
        $this->container['feedbackCount'] = isset($data['feedbackCount']) ? $data['feedbackCount'] : null;
        $this->container['feedbackRating'] = isset($data['feedbackRating']) ? $data['feedbackRating'] : null;
        $this->container['id'] = isset($data['id']) ? $data['id'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets defaultShippingCountry
     *
     * @return string
     */
    public function getDefaultShippingCountry()
    {
        return $this->container['defaultShippingCountry'];
    }

    /**
     * Sets defaultShippingCountry
     *
     * @param string $defaultShippingCountry defaultShippingCountry
     *
     * @return $this
     */
    public function setDefaultShippingCountry($defaultShippingCountry)
    {
        $this->container['defaultShippingCountry'] = $defaultShippingCountry;

        return $this;
    }

    /**
     * Gets feedbackCount
     *
     * @return int
     */
    public function getFeedbackCount()
    {
        return $this->container['feedbackCount'];
    }

    /**
     * Sets feedbackCount
     *
     * @param int $feedbackCount feedbackCount
     *
     * @return $this
     */
    public function setFeedbackCount($feedbackCount)
    {
        $this->container['feedbackCount'] = $feedbackCount;

        return $this;
    }

    /**
     * Gets feedbackRating
     *
     * @return float
     */
    public function getFeedbackRating()
    {
        return $this->container['feedbackRating'];
    }

    /**
     * Sets feedbackRating
     *
     * @param float $feedbackRating feedbackRating
     *
     * @return $this
     */
    public function setFeedbackRating($feedbackRating)
    {
        $this->container['feedbackRating'] = $feedbackRating;

        return $this;
    }

    /**
     * Gets id
     *
     * @return string
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param string $id id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


