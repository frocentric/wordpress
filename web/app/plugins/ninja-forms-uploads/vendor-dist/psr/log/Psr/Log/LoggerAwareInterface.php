<?php

namespace NF_FU_VENDOR\Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(\NF_FU_VENDOR\Psr\Log\LoggerInterface $logger);
}
