<?php

namespace NF_FU_VENDOR\Aws\Api\Serializer;

use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Aws\Api\Service;
/**
 * @internal
 */
class RestXmlSerializer extends \NF_FU_VENDOR\Aws\Api\Serializer\RestSerializer
{
    /** @var XmlBody */
    private $xmlBody;
    /**
     * @param Service $api      Service API description
     * @param string  $endpoint Endpoint to connect to
     * @param XmlBody $xmlBody  Optional XML formatter to use
     */
    public function __construct(\NF_FU_VENDOR\Aws\Api\Service $api, $endpoint, \NF_FU_VENDOR\Aws\Api\Serializer\XmlBody $xmlBody = null)
    {
        parent::__construct($api, $endpoint);
        $this->xmlBody = $xmlBody ?: new \NF_FU_VENDOR\Aws\Api\Serializer\XmlBody($api);
    }
    protected function payload(\NF_FU_VENDOR\Aws\Api\StructureShape $member, array $value, array &$opts)
    {
        $opts['headers']['Content-Type'] = 'application/xml';
        $opts['body'] = (string) $this->xmlBody->build($member, $value);
    }
}
