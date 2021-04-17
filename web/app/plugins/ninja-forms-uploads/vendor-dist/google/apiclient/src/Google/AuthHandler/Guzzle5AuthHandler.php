<?php

namespace NF_FU_VENDOR;

use NF_FU_VENDOR\Google\Auth\CredentialsLoader;
use NF_FU_VENDOR\Google\Auth\HttpHandler\HttpHandlerFactory;
use NF_FU_VENDOR\Google\Auth\FetchAuthTokenCache;
use NF_FU_VENDOR\Google\Auth\Subscriber\AuthTokenSubscriber;
use NF_FU_VENDOR\Google\Auth\Subscriber\ScopedAccessTokenSubscriber;
use NF_FU_VENDOR\Google\Auth\Subscriber\SimpleSubscriber;
use NF_FU_VENDOR\GuzzleHttp\Client;
use NF_FU_VENDOR\GuzzleHttp\ClientInterface;
use NF_FU_VENDOR\Psr\Cache\CacheItemPoolInterface;
/**
*
*/
class Google_AuthHandler_Guzzle5AuthHandler
{
    protected $cache;
    protected $cacheConfig;
    public function __construct(\NF_FU_VENDOR\Psr\Cache\CacheItemPoolInterface $cache = null, array $cacheConfig = [])
    {
        $this->cache = $cache;
        $this->cacheConfig = $cacheConfig;
    }
    public function attachCredentials(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $http, \NF_FU_VENDOR\Google\Auth\CredentialsLoader $credentials, callable $tokenCallback = null)
    {
        // use the provided cache
        if ($this->cache) {
            $credentials = new \NF_FU_VENDOR\Google\Auth\FetchAuthTokenCache($credentials, $this->cacheConfig, $this->cache);
        }
        // if we end up needing to make an HTTP request to retrieve credentials, we
        // can use our existing one, but we need to throw exceptions so the error
        // bubbles up.
        $authHttp = $this->createAuthHttp($http);
        $authHttpHandler = \NF_FU_VENDOR\Google\Auth\HttpHandler\HttpHandlerFactory::build($authHttp);
        $subscriber = new \NF_FU_VENDOR\Google\Auth\Subscriber\AuthTokenSubscriber($credentials, $authHttpHandler, $tokenCallback);
        $http->setDefaultOption('auth', 'google_auth');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    public function attachToken(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $http, array $token, array $scopes)
    {
        $tokenFunc = function ($scopes) use($token) {
            return $token['access_token'];
        };
        $subscriber = new \NF_FU_VENDOR\Google\Auth\Subscriber\ScopedAccessTokenSubscriber($tokenFunc, $scopes, $this->cacheConfig, $this->cache);
        $http->setDefaultOption('auth', 'scoped');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    public function attachKey(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $http, $key)
    {
        $subscriber = new \NF_FU_VENDOR\Google\Auth\Subscriber\SimpleSubscriber(['key' => $key]);
        $http->setDefaultOption('auth', 'simple');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    private function createAuthHttp(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $http)
    {
        return new \NF_FU_VENDOR\GuzzleHttp\Client(['base_url' => $http->getBaseUrl(), 'defaults' => ['exceptions' => \true, 'verify' => $http->getDefaultOption('verify'), 'proxy' => $http->getDefaultOption('proxy')]]);
    }
}
