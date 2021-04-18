<?php

namespace NF_FU_VENDOR\Aws\Api\ErrorParser;

use NF_FU_VENDOR\Aws\Api\Parser\PayloadParserTrait;
use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
/**
 * Provides basic JSON error parsing functionality.
 */
trait JsonParserTrait
{
    use PayloadParserTrait;
    private function genericHandler(\NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response)
    {
        $code = (string) $response->getStatusCode();
        return ['request_id' => (string) $response->getHeaderLine('x-amzn-requestid'), 'code' => null, 'message' => null, 'type' => $code[0] == '4' ? 'client' : 'server', 'parsed' => $this->parseJson($response->getBody(), $response)];
    }
    protected function payload(\NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response, \NF_FU_VENDOR\Aws\Api\StructureShape $member)
    {
        $jsonBody = $this->parseJson($response->getBody(), $response);
        if ($jsonBody) {
            return $this->parser->parse($member, $jsonBody);
        }
    }
}
