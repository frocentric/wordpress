<?php

namespace NF_FU_VENDOR\Aws\Signature;

use NF_FU_VENDOR\Aws\Credentials\CredentialsInterface;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface;
/**
 * Amazon S3 signature version 4 support.
 */
class S3SignatureV4 extends \NF_FU_VENDOR\Aws\Signature\SignatureV4
{
    /**
     * Always add a x-amz-content-sha-256 for data integrity.
     */
    public function signRequest(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, \NF_FU_VENDOR\Aws\Credentials\CredentialsInterface $credentials)
    {
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader('X-Amz-Content-Sha256', $this->getPayload($request));
        }
        return parent::signRequest($request, $credentials);
    }
    /**
     * Always add a x-amz-content-sha-256 for data integrity.
     */
    public function presign(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, \NF_FU_VENDOR\Aws\Credentials\CredentialsInterface $credentials, $expires, array $options = [])
    {
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader('X-Amz-Content-Sha256', $this->getPresignedPayload($request));
        }
        return parent::presign($request, $credentials, $expires, $options);
    }
    /**
     * Override used to allow pre-signed URLs to be created for an
     * in-determinate request payload.
     */
    protected function getPresignedPayload(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request)
    {
        return \NF_FU_VENDOR\Aws\Signature\SignatureV4::UNSIGNED_PAYLOAD;
    }
    /**
     * Amazon S3 does not double-encode the path component in the canonical request
     */
    protected function createCanonicalizedPath($path)
    {
        // Only remove one slash in case of keys that have a preceding slash
        if (\substr($path, 0, 1) === '/') {
            $path = \substr($path, 1);
        }
        return '/' . $path;
    }
}
