<?php

namespace NF_FU_VENDOR\Aws\Api\Parser;

use NF_FU_VENDOR\Aws\Api\Service;
use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
use NF_FU_VENDOR\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements REST-JSON parsing (e.g., Glacier, Elastic Transcoder)
 */
class RestJsonParser extends \NF_FU_VENDOR\Aws\Api\Parser\AbstractRestParser
{
    use PayloadParserTrait;
    /**
     * @param Service    $api    Service description
     * @param JsonParser $parser JSON body builder
     */
    public function __construct(\NF_FU_VENDOR\Aws\Api\Service $api, \NF_FU_VENDOR\Aws\Api\Parser\JsonParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new \NF_FU_VENDOR\Aws\Api\Parser\JsonParser();
    }
    protected function payload(\NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response, \NF_FU_VENDOR\Aws\Api\StructureShape $member, array &$result)
    {
        $jsonBody = $this->parseJson($response->getBody(), $response);
        if ($jsonBody) {
            $result += $this->parser->parse($member, $jsonBody);
        }
    }
    public function parseMemberFromStream(\NF_FU_VENDOR\Psr\Http\Message\StreamInterface $stream, \NF_FU_VENDOR\Aws\Api\StructureShape $member, $response)
    {
        $jsonBody = $this->parseJson($stream, $response);
        if ($jsonBody) {
            return $this->parser->parse($member, $jsonBody);
        }
        return [];
    }
}
