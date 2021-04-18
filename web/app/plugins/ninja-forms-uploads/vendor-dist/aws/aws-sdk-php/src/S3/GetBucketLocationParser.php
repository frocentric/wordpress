<?php

namespace NF_FU_VENDOR\Aws\S3;

use NF_FU_VENDOR\Aws\Api\Parser\AbstractParser;
use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
use NF_FU_VENDOR\Psr\Http\Message\StreamInterface;
/**
 * @internal Decorates a parser for the S3 service to correctly handle the
 *           GetBucketLocation operation.
 */
class GetBucketLocationParser extends \NF_FU_VENDOR\Aws\Api\Parser\AbstractParser
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
        $fn = $this->parser;
        $result = $fn($command, $response);
        if ($command->getName() === 'GetBucketLocation') {
            $location = 'us-east-1';
            if (\preg_match('/>(.+?)<\\/LocationConstraint>/', $response->getBody(), $matches)) {
                $location = $matches[1] === 'EU' ? 'eu-west-1' : $matches[1];
            }
            $result['LocationConstraint'] = $location;
        }
        return $result;
    }
    public function parseMemberFromStream(\NF_FU_VENDOR\Psr\Http\Message\StreamInterface $stream, \NF_FU_VENDOR\Aws\Api\StructureShape $member, $response)
    {
        return $this->parser->parseMemberFromStream($stream, $member, $response);
    }
}
