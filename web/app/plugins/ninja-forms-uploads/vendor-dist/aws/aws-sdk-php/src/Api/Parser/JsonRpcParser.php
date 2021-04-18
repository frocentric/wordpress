<?php

namespace NF_FU_VENDOR\Aws\Api\Parser;

use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Aws\Api\Service;
use NF_FU_VENDOR\Aws\Result;
use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
use NF_FU_VENDOR\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements JSON-RPC parsing (e.g., DynamoDB)
 */
class JsonRpcParser extends \NF_FU_VENDOR\Aws\Api\Parser\AbstractParser
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
    public function __invoke(\NF_FU_VENDOR\Aws\CommandInterface $command, \NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response)
    {
        $operation = $this->api->getOperation($command->getName());
        $result = null === $operation['output'] ? null : $this->parseMemberFromStream($response->getBody(), $operation->getOutput(), $response);
        return new \NF_FU_VENDOR\Aws\Result($result ?: []);
    }
    public function parseMemberFromStream(\NF_FU_VENDOR\Psr\Http\Message\StreamInterface $stream, \NF_FU_VENDOR\Aws\Api\StructureShape $member, $response)
    {
        return $this->parser->parse($member, $this->parseJson($stream, $response));
    }
}
