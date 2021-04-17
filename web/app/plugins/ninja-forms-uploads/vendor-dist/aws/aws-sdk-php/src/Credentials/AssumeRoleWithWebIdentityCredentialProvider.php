<?php

namespace NF_FU_VENDOR\Aws\Credentials;

use NF_FU_VENDOR\Aws\Exception\AwsException;
use NF_FU_VENDOR\Aws\Exception\CredentialsException;
use NF_FU_VENDOR\Aws\Result;
use NF_FU_VENDOR\Aws\Sts\StsClient;
use NF_FU_VENDOR\GuzzleHttp\Promise;
/**
 * Credential provider that provides credentials via assuming a role with a web identity
 * More Information, see: https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sts-2011-06-15.html#assumerolewithwebidentity
 */
class AssumeRoleWithWebIdentityCredentialProvider
{
    const ERROR_MSG = "Missing required 'AssumeRoleWithWebIdentityCredentialProvider' configuration option: ";
    const ENV_RETRIES = 'AWS_METADATA_SERVICE_NUM_ATTEMPTS';
    /** @var string */
    private $tokenFile;
    /** @var string */
    private $arn;
    /** @var string */
    private $session;
    /** @var StsClient */
    private $client;
    /** @var integer */
    private $retries;
    /** @var integer */
    private $attempts;
    /**
     * The constructor attempts to load config from environment variables.
     * If not set, the following config options are used:
     *  - WebIdentityTokenFile: full path of token filename
     *  - RoleArn: arn of role to be assumed
     *  - SessionName: (optional) set by SDK if not provided
     *
     * @param array $config Configuration options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['RoleArn'])) {
            throw new \InvalidArgumentException(self::ERROR_MSG . "'RoleArn'.");
        }
        $this->arn = $config['RoleArn'];
        if (!isset($config['WebIdentityTokenFile'])) {
            throw new \InvalidArgumentException(self::ERROR_MSG . "'WebIdentityTokenFile'.");
        }
        $this->tokenFile = $config['WebIdentityTokenFile'];
        if (!\preg_match("/^\\w\\:|^\\/|^\\\\/", $this->tokenFile)) {
            throw new \InvalidArgumentException("'WebIdentityTokenFile' must be an absolute path.");
        }
        $this->retries = (int) \getenv(self::ENV_RETRIES) ?: (isset($config['retries']) ? $config['retries'] : 3);
        $this->attempts = 0;
        $this->session = isset($config['SessionName']) ? $config['SessionName'] : 'aws-sdk-php-' . \round(\microtime(\true) * 1000);
        $region = isset($config['region']) ? $config['region'] : 'us-east-1';
        if (isset($config['client'])) {
            $this->client = $config['client'];
        } else {
            $this->client = new \NF_FU_VENDOR\Aws\Sts\StsClient(['credentials' => \false, 'region' => $region, 'version' => 'latest']);
        }
    }
    /**
     * Loads assume role with web identity credentials.
     *
     * @return Promise\PromiseInterface
     */
    public function __invoke()
    {
        return \NF_FU_VENDOR\GuzzleHttp\Promise\coroutine(function () {
            $client = $this->client;
            $result = null;
            while ($result == null) {
                try {
                    $token = \file_get_contents($this->tokenFile);
                } catch (\Exception $exception) {
                    throw new \NF_FU_VENDOR\Aws\Exception\CredentialsException("Error reading WebIdentityTokenFile from " . $this->tokenFile, 0, $exception);
                }
                $assumeParams = ['RoleArn' => $this->arn, 'RoleSessionName' => $this->session, 'WebIdentityToken' => $token];
                try {
                    $result = $client->assumeRoleWithWebIdentity($assumeParams);
                } catch (\NF_FU_VENDOR\Aws\Exception\AwsException $e) {
                    if ($e->getAwsErrorCode() == 'InvalidIdentityToken') {
                        if ($this->attempts < $this->retries) {
                            \sleep(\pow(1.2, $this->attempts));
                        } else {
                            throw new \NF_FU_VENDOR\Aws\Exception\CredentialsException("InvalidIdentityToken, retries exhausted");
                        }
                    } else {
                        throw new \NF_FU_VENDOR\Aws\Exception\CredentialsException("Error assuming role from web identity credentials", 0, $e);
                    }
                } catch (\Exception $e) {
                    throw new \NF_FU_VENDOR\Aws\Exception\CredentialsException("Error retrieving web identity credentials: " . $e->getMessage() . " (" . $e->getCode() . ")");
                }
                $this->attempts++;
            }
            (yield $this->client->createCredentials($result));
        });
    }
}
