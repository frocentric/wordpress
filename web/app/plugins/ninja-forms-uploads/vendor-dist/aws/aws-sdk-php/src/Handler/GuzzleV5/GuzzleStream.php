<?php

namespace NF_FU_VENDOR\Aws\Handler\GuzzleV5;

use NF_FU_VENDOR\GuzzleHttp\Stream\StreamDecoratorTrait;
use NF_FU_VENDOR\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;
use NF_FU_VENDOR\Psr\Http\Message\StreamInterface as Psr7StreamInterface;
/**
 * Adapts a PSR-7 Stream to a Guzzle 5 Stream.
 *
 * @codeCoverageIgnore
 */
class GuzzleStream implements \NF_FU_VENDOR\GuzzleHttp\Stream\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var Psr7StreamInterface */
    private $stream;
    public function __construct(\NF_FU_VENDOR\Psr\Http\Message\StreamInterface $stream)
    {
        $this->stream = $stream;
    }
}
