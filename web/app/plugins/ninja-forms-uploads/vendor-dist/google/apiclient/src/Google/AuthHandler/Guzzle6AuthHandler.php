<?php

namespace NF_FU_VENDOR;

use NF_FU_VENDOR\Google\Auth\CredentialsLoader;
use NF_FU_VENDOR\Google\Auth\HttpHandler\HttpHandlerFactory;
use NF_FU_VENDOR\Google\Auth\FetchAuthTokenCache;
use NF_FU_VENDOR\Google\Auth\Middleware\AuthTokenMiddleware;
use NF_FU_VENDOR\Google\Auth\Middleware\ScopedAccessTokenMiddleware;
use NF_FU_VENDOR\Google\Auth\Middleware\SimpleMiddleware;
use NF_FU_VENDOR\GuzzleHttp\Client;
use NF_FU_VENDOR\GuzzleHttp\ClientInterface;
use NF_FU_VENDOR\Psr\Cache\CacheItemPoolInterface;
/**
*
*/
class Google_AuthHandler_Guzzle6AuthHandler
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
        $middleware = new \NF_FU_VENDOR\Google\Auth\Middleware\AuthTokenMiddleware($credentials, $authHttpHandler, $tokenCallback);
        $config = $http->getConfig();
        $config['handler']->remove('google_auth');
        $config['handler']->push($middleware, 'google_auth');
        $config['auth'] = 'google_auth';
        $http = new \NF_FU_VENDOR\GuzzleHttp\Client($config);
        return $http;
    }
    public function attachToken(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $http, array $token, array $scopes)
    {
        $tokenFunc = function ($scopes) use($token) {
            return $token['access_token'];
        };
        $middleware = new \NF_FU_VENDOR\Google\Auth\Middleware\ScopedAccessTokenMiddleware($tokenFunc, $scopes, $this->cacheConfig, $this->cache);
        $config = $http->getConfig();
        $config['handler']->remove('google_auth');
        $config['handler']->push($middleware, 'google_auth');
        $config['auth'] = 'scoped';
        $http = new \NF_FU_VENDOR\GuzzleHttp\Client($config);
        return $http;
    }
    public function attachKey(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $http, $key)
    {
        $middleware = new \NF_FU_VENDOR\Google\Auth\Middleware\SimpleMiddleware(['key' => $key]);
        $config = $http->getConfig();
        $config['handler']->remove('google_auth');
        $config['handler']->push($middleware, 'google_auth');
        $config['auth'] = 'simple';
        $http = new \NF_FU_VENDOR\GuzzleHttp\Client($config);
        return $http;
    }
    private function createAuthHttp(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $http)
    {
        return new \NF_FU_VENDOR\GuzzleHttp\Client(['base_uri' => $http->getConfig('base_uri'), 'exceptions' => \true, 'verify' => $http->getConfig('verify'), 'proxy' => $http->getConfig('proxy')]);
    }
}
