<?php

namespace NF_FU_VENDOR\Aws;

use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
interface ResponseContainerInterface
{
    /**
     * Get the received HTTP response if any.
     *
     * @return ResponseInterface|null
     */
    public function getResponse();
}
