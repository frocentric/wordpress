<?php

namespace NF_FU_VENDOR\Aws\Api\Parser;

use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\Aws\Exception\AwsException;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
use NF_FU_VENDOR\Psr\Http\Message\StreamInterface;
use NF_FU_VENDOR\GuzzleHttp\Psr7;
/**
 * @internal Decorates a parser and validates the x-amz-crc32 header.
 */
class Crc32ValidatingParser extends \NF_FU_VENDOR\Aws\Api\Parser\AbstractParser
{
    /**
     * @param callable $parser Parser to wrap.
     */
    public function __construct(callable $parser)
    {
        $this->parser = $parser;
    }
    public function __invoke(\NF_FU_VENDOR\Aws\CommandInterface $command, \NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response)
    {
        if ($expected = $response->getHeaderLine('x-amz-crc32')) {
            $hash = \hexdec(\NF_FU_VENDOR\GuzzleHttp\Psr7\hash($response->getBody(), 'crc32b'));
            if ($expected != $hash) {
                throw new \NF_FU_VENDOR\Aws\Exception\AwsException("crc32 mismatch. Expected {$expected}, found {$hash}.", $command, ['code' => 'ClientChecksumMismatch', 'connection_error' => \true, 'response' => $response]);
            }
        }
        $fn = $this->parser;
        return $fn($command, $response);
    }
    public function parseMemberFromStream(\NF_FU_VENDOR\Psr\Http\Message\StreamInterface $stream, \NF_FU_VENDOR\Aws\Api\StructureShape $member, $response)
    {
        return $this->parser->parseMemberFromStream($stream, $member, $response);
    }
}
