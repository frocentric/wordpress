<?php

namespace NF_FU_VENDOR\Aws\Signature;

use NF_FU_VENDOR\Aws\Credentials\CredentialsInterface;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface;
/**
 * Provides anonymous client access (does not sign requests).
 */
class AnonymousSignature implements \NF_FU_VENDOR\Aws\Signature\SignatureInterface
{
    public function signRequest(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, \NF_FU_VENDOR\Aws\Credentials\CredentialsInterface $credentials)
    {
        return $request;
    }
    public function presign(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, \NF_FU_VENDOR\Aws\Credentials\CredentialsInterface $credentials, $expires, array $options = [])
    {
        return $request;
    }
}
