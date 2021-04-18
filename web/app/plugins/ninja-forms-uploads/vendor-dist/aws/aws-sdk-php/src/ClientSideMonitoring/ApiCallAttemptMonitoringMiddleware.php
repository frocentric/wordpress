<?php

namespace NF_FU_VENDOR\Aws\ClientSideMonitoring;

use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\Aws\Credentials\CredentialsInterface;
use NF_FU_VENDOR\Aws\Exception\AwsException;
use NF_FU_VENDOR\Aws\ResponseContainerInterface;
use NF_FU_VENDOR\Aws\ResultInterface;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
/**
 * @internal
 */
class ApiCallAttemptMonitoringMiddleware extends \NF_FU_VENDOR\Aws\ClientSideMonitoring\AbstractMonitoringMiddleware
{
    /**
     * Standard middleware wrapper function with CSM options passed in.
     *
     * @param callable $credentialProvider
     * @param mixed  $options
     * @param string $region
     * @param string $service
     * @return callable
     */
    public static function wrap(callable $credentialProvider, $options, $region, $service)
    {
        return function (callable $handler) use($credentialProvider, $options, $region, $service) {
            return new static($handler, $credentialProvider, $options, $region, $service);
        };
    }
    /**
     * {@inheritdoc}
     */
    public static function getRequestData(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request)
    {
        return ['Fqdn' => $request->getUri()->getHost()];
    }
    /**
     * {@inheritdoc}
     */
    public static function getResponseData($klass)
    {
        if ($klass instanceof \NF_FU_VENDOR\Aws\ResultInterface) {
            return ['AttemptLatency' => self::getResultAttemptLatency($klass), 'DestinationIp' => self::getResultDestinationIp($klass), 'DnsLatency' => self::getResultDnsLatency($klass), 'HttpStatusCode' => self::getResultHttpStatusCode($klass), 'XAmzId2' => self::getResultHeader($klass, 'x-amz-id-2'), 'XAmzRequestId' => self::getResultHeader($klass, 'x-amz-request-id'), 'XAmznRequestId' => self::getResultHeader($klass, 'x-amzn-RequestId')];
        }
        if ($klass instanceof \NF_FU_VENDOR\Aws\Exception\AwsException) {
            return ['AttemptLatency' => self::getAwsExceptionAttemptLatency($klass), 'AwsException' => \substr(self::getAwsExceptionErrorCode($klass), 0, 128), 'AwsExceptionMessage' => \substr(self::getAwsExceptionMessage($klass), 0, 512), 'DestinationIp' => self::getAwsExceptionDestinationIp($klass), 'DnsLatency' => self::getAwsExceptionDnsLatency($klass), 'HttpStatusCode' => self::getAwsExceptionHttpStatusCode($klass), 'XAmzId2' => self::getAwsExceptionHeader($klass, 'x-amz-id-2'), 'XAmzRequestId' => self::getAwsExceptionHeader($klass, 'x-amz-request-id'), 'XAmznRequestId' => self::getAwsExceptionHeader($klass, 'x-amzn-RequestId')];
        }
        if ($klass instanceof \Exception) {
            return ['HttpStatusCode' => self::getExceptionHttpStatusCode($klass), 'SdkException' => \substr(self::getExceptionCode($klass), 0, 128), 'SdkExceptionMessage' => \substr(self::getExceptionMessage($klass), 0, 512), 'XAmzId2' => self::getExceptionHeader($klass, 'x-amz-id-2'), 'XAmzRequestId' => self::getExceptionHeader($klass, 'x-amz-request-id'), 'XAmznRequestId' => self::getExceptionHeader($klass, 'x-amzn-RequestId')];
        }
        throw new \InvalidArgumentException('Parameter must be an instance of ResultInterface, AwsException or Exception.');
    }
    private static function getResultAttemptLatency(\NF_FU_VENDOR\Aws\ResultInterface $result)
    {
        if (isset($result['@metadata']['transferStats']['http'])) {
            $attempt = \end($result['@metadata']['transferStats']['http']);
            if (isset($attempt['total_time'])) {
                return (int) \floor($attempt['total_time'] * 1000);
            }
        }
        return null;
    }
    private static function getResultDestinationIp(\NF_FU_VENDOR\Aws\ResultInterface $result)
    {
        if (isset($result['@metadata']['transferStats']['http'])) {
            $attempt = \end($result['@metadata']['transferStats']['http']);
            if (isset($attempt['primary_ip'])) {
                return $attempt['primary_ip'];
            }
        }
        return null;
    }
    private static function getResultDnsLatency(\NF_FU_VENDOR\Aws\ResultInterface $result)
    {
        if (isset($result['@metadata']['transferStats']['http'])) {
            $attempt = \end($result['@metadata']['transferStats']['http']);
            if (isset($attempt['namelookup_time'])) {
                return (int) \floor($attempt['namelookup_time'] * 1000);
            }
        }
        return null;
    }
    private static function getResultHttpStatusCode(\NF_FU_VENDOR\Aws\ResultInterface $result)
    {
        return $result['@metadata']['statusCode'];
    }
    private static function getAwsExceptionAttemptLatency(\NF_FU_VENDOR\Aws\Exception\AwsException $e)
    {
        $attempt = $e->getTransferInfo();
        if (isset($attempt['total_time'])) {
            return (int) \floor($attempt['total_time'] * 1000);
        }
        return null;
    }
    private static function getAwsExceptionErrorCode(\NF_FU_VENDOR\Aws\Exception\AwsException $e)
    {
        return $e->getAwsErrorCode();
    }
    private static function getAwsExceptionMessage(\NF_FU_VENDOR\Aws\Exception\AwsException $e)
    {
        return $e->getAwsErrorMessage();
    }
    private static function getAwsExceptionDestinationIp(\NF_FU_VENDOR\Aws\Exception\AwsException $e)
    {
        $attempt = $e->getTransferInfo();
        if (isset($attempt['primary_ip'])) {
            return $attempt['primary_ip'];
        }
        return null;
    }
    private static function getAwsExceptionDnsLatency(\NF_FU_VENDOR\Aws\Exception\AwsException $e)
    {
        $attempt = $e->getTransferInfo();
        if (isset($attempt['namelookup_time'])) {
            return (int) \floor($attempt['namelookup_time'] * 1000);
        }
        return null;
    }
    private static function getAwsExceptionHttpStatusCode(\NF_FU_VENDOR\Aws\Exception\AwsException $e)
    {
        $response = $e->getResponse();
        if ($response !== null) {
            return $response->getStatusCode();
        }
        return null;
    }
    private static function getExceptionHttpStatusCode(\Exception $e)
    {
        if ($e instanceof \NF_FU_VENDOR\Aws\ResponseContainerInterface) {
            $response = $e->getResponse();
            if ($response instanceof \NF_FU_VENDOR\Psr\Http\Message\ResponseInterface) {
                return $response->getStatusCode();
            }
        }
        return null;
    }
    private static function getExceptionCode(\Exception $e)
    {
        if (!$e instanceof \NF_FU_VENDOR\Aws\Exception\AwsException) {
            return \get_class($e);
        }
        return null;
    }
    private static function getExceptionMessage(\Exception $e)
    {
        if (!$e instanceof \NF_FU_VENDOR\Aws\Exception\AwsException) {
            return $e->getMessage();
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    protected function populateRequestEventData(\NF_FU_VENDOR\Aws\CommandInterface $cmd, \NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, array $event)
    {
        $event = parent::populateRequestEventData($cmd, $request, $event);
        $event['Type'] = 'ApiCallAttempt';
        return $event;
    }
    /**
     * {@inheritdoc}
     */
    protected function populateResultEventData($result, array $event)
    {
        $event = parent::populateResultEventData($result, $event);
        $provider = $this->credentialProvider;
        /** @var CredentialsInterface $credentials */
        $credentials = $provider()->wait();
        $event['AccessKey'] = $credentials->getAccessKeyId();
        $sessionToken = $credentials->getSecurityToken();
        if ($sessionToken !== null) {
            $event['SessionToken'] = $sessionToken;
        }
        if (empty($event['AttemptLatency'])) {
            $event['AttemptLatency'] = (int) (\floor(\microtime(\true) * 1000) - $event['Timestamp']);
        }
        return $event;
    }
}
