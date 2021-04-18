<?php

namespace NF_FU_VENDOR\Google\Auth\HttpHandler;

use NF_FU_VENDOR\GuzzleHttp\ClientInterface;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
class Guzzle6HttpHandler
{
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @param ClientInterface $client
     */
    public function __construct(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $client)
    {
        $this->client = $client;
    }
    /**
     * Accepts a PSR-7 request and an array of options and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function __invoke(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        return $this->client->send($request, $options);
    }
    /**
     * Accepts a PSR-7 request and an array of options and returns a PromiseInterface
     *
     * @param RequestInterface $request
     * @param array $options
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function async(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        return $this->client->sendAsync($request, $options);
    }
}
