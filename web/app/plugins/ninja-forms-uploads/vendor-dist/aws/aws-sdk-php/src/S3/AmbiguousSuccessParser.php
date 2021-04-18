<?php

namespace NF_FU_VENDOR\Aws\S3;

use NF_FU_VENDOR\Aws\Api\Parser\AbstractParser;
use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\Aws\Exception\AwsException;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
use NF_FU_VENDOR\Psr\Http\Message\StreamInterface;
/**
 * Converts errors returned with a status code of 200 to a retryable error type.
 *
 * @internal
 */
class AmbiguousSuccessParser extends \NF_FU_VENDOR\Aws\Api\Parser\AbstractParser
{
    private static $ambiguousSuccesses = ['UploadPartCopy' => \true, 'CopyObject' => \true, 'CompleteMultipartUpload' => \true];
    /** @var callable */
    private $errorParser;
    /** @var string */
    private $exceptionClass;
    public function __construct(callable $parser, callable $errorParser, $exceptionClass = \NF_FU_VENDOR\Aws\Exception\AwsException::class)
    {
        $this->parser = $parser;
        $this->errorParser = $errorParser;
        $this->exceptionClass = $exceptionClass;
    }
    public function __invoke(\NF_FU_VENDOR\Aws\CommandInterface $command, \NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response)
    {
        if (200 === $response->getStatusCode() && isset(self::$ambiguousSuccesses[$command->getName()])) {
            $errorParser = $this->errorParser;
            $parsed = $errorParser($response);
            if (isset($parsed['code']) && isset($parsed['message'])) {
                throw new $this->exceptionClass($parsed['message'], $command, ['connection_error' => \true]);
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
