<?php

namespace NF_FU_VENDOR\Aws\Signature;

use NF_FU_VENDOR\Aws\Credentials\CredentialsInterface;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface;
/**
 * Interface used to provide interchangeable strategies for signing requests
 * using the various AWS signature protocols.
 */
interface SignatureInterface
{
    /**
     * Signs the specified request with an AWS signing protocol by using the
     * provided AWS account credentials and adding the required headers to the
     * request.
     *
     * @param RequestInterface     $request     Request to sign
     * @param CredentialsInterface $credentials Signing credentials
     *
     * @return RequestInterface Returns the modified request.
     */
    public function signRequest(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, \NF_FU_VENDOR\Aws\Credentials\CredentialsInterface $credentials);
    /**
     * Create a pre-signed request.
     *
     * @param RequestInterface     $request     Request to sign
     * @param CredentialsInterface $credentials Credentials used to sign
     * @param int|string|\DateTime $expires The time at which the URL should
     *     expire. This can be a Unix timestamp, a PHP DateTime object, or a
     *     string that can be evaluated by strtotime.
     *
     * @return RequestInterface
     */
    public function presign(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, \NF_FU_VENDOR\Aws\Credentials\CredentialsInterface $credentials, $expires, array $options = []);
}
