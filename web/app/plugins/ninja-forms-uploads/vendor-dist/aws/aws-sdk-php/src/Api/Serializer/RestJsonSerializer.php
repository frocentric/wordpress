<?php

namespace NF_FU_VENDOR\Aws\Api\Serializer;

use NF_FU_VENDOR\Aws\Api\Service;
use NF_FU_VENDOR\Aws\Api\StructureShape;
/**
 * Serializes requests for the REST-JSON protocol.
 * @internal
 */
class RestJsonSerializer extends \NF_FU_VENDOR\Aws\Api\Serializer\RestSerializer
{
    /** @var JsonBody */
    private $jsonFormatter;
    /** @var string */
    private $contentType;
    /**
     * @param Service  $api           Service API description
     * @param string   $endpoint      Endpoint to connect to
     * @param JsonBody $jsonFormatter Optional JSON formatter to use
     */
    public function __construct(\NF_FU_VENDOR\Aws\Api\Service $api, $endpoint, \NF_FU_VENDOR\Aws\Api\Serializer\JsonBody $jsonFormatter = null)
    {
        parent::__construct($api, $endpoint);
        $this->contentType = 'application/json';
        $this->jsonFormatter = $jsonFormatter ?: new \NF_FU_VENDOR\Aws\Api\Serializer\JsonBody($api);
    }
    protected function payload(\NF_FU_VENDOR\Aws\Api\StructureShape $member, array $value, array &$opts)
    {
        $opts['headers']['Content-Type'] = $this->contentType;
        $opts['body'] = (string) $this->jsonFormatter->build($member, $value);
    }
}
