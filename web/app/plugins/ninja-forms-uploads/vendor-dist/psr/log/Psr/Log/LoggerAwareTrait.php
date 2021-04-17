<?php

namespace NF_FU_VENDOR\Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(\NF_FU_VENDOR\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
