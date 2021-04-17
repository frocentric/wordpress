<?php

namespace NF_FU_VENDOR\Aws\ClientSideMonitoring;

use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\Aws\Exception\AwsException;
use NF_FU_VENDOR\Aws\ResultInterface;
use NF_FU_VENDOR\GuzzleHttp\Psr7\Request;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface;
/**
 * @internal
 */
interface MonitoringMiddlewareInterface
{
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param RequestInterface $request
     * @return array
     */
    public static function getRequestData(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request);
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param ResultInterface|AwsException|\Exception $klass
     * @return array
     */
    public static function getResponseData($klass);
    public function __invoke(\NF_FU_VENDOR\Aws\CommandInterface $cmd, \NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request);
}
