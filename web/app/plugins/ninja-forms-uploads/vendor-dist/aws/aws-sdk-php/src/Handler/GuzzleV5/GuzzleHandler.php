<?php

namespace NF_FU_VENDOR\Aws\Handler\GuzzleV5;

use Exception;
use NF_FU_VENDOR\GuzzleHttp\Client;
use NF_FU_VENDOR\GuzzleHttp\ClientInterface;
use NF_FU_VENDOR\GuzzleHttp\Event\EndEvent;
use NF_FU_VENDOR\GuzzleHttp\Exception\ConnectException;
use NF_FU_VENDOR\GuzzleHttp\Exception\RequestException;
use NF_FU_VENDOR\GuzzleHttp\Message\ResponseInterface as GuzzleResponse;
use NF_FU_VENDOR\GuzzleHttp\Promise;
use NF_FU_VENDOR\GuzzleHttp\Psr7\Response as Psr7Response;
use NF_FU_VENDOR\GuzzleHttp\Stream\Stream;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface as Psr7Request;
use NF_FU_VENDOR\Psr\Http\Message\StreamInterface as Psr7StreamInterface;
/**
 * A request handler that sends PSR-7-compatible requests with Guzzle 5.
 *
 * The handler accepts a PSR-7 Request object and an array of transfer options
 * and returns a Guzzle 6 Promise. The promise is either resolved with a
 * PSR-7 Response object or rejected with an array of error data.
 *
 * @codeCoverageIgnore
 */
class GuzzleHandler
{
    private static $validOptions = ['proxy' => \true, 'expect' => \true, 'cert' => \true, 'verify' => \true, 'timeout' => \true, 'debug' => \true, 'connect_timeout' => \true, 'stream' => \true, 'delay' => \true, 'sink' => \true];
    /** @var ClientInterface */
    private $client;
    /**
     * @param ClientInterface $client
     */
    public function __construct(\NF_FU_VENDOR\GuzzleHttp\ClientInterface $client = null)
    {
        $this->client = $client ?: new \NF_FU_VENDOR\GuzzleHttp\Client();
    }
    /**
     * @param Psr7Request $request
     * @param array $options
     * @return Promise\Promise|Promise\PromiseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        // Create and send a Guzzle 5 request
        $guzzlePromise = $this->client->send($this->createGuzzleRequest($request, $options));
        $promise = new \NF_FU_VENDOR\GuzzleHttp\Promise\Promise(function () use($guzzlePromise) {
            try {
                $guzzlePromise->wait();
            } catch (\Exception $e) {
                // The promise is already delivered when the exception is
                // thrown, so don't rethrow it.
            }
        }, [$guzzlePromise, 'cancel']);
        $guzzlePromise->then([$promise, 'resolve'], [$promise, 'reject']);
        return $promise->then(function (\NF_FU_VENDOR\GuzzleHttp\Message\ResponseInterface $response) {
            // Adapt the Guzzle 5 Future to a Guzzle 6 ResponsePromise.
            return $this->createPsr7Response($response);
        }, function (\Exception $exception) use($options) {
            // If we got a 'sink' that's a path, set the response body to
            // the contents of the file. This will build the resulting
            // exception with more information.
            if ($exception instanceof \NF_FU_VENDOR\GuzzleHttp\Exception\RequestException) {
                if (isset($options['sink'])) {
                    if (!$options['sink'] instanceof \NF_FU_VENDOR\Psr\Http\Message\StreamInterface) {
                        $exception->getResponse()->setBody(\NF_FU_VENDOR\GuzzleHttp\Stream\Stream::factory(\file_get_contents($options['sink'])));
                    }
                }
            }
            // Reject with information about the error.
            return new \NF_FU_VENDOR\GuzzleHttp\Promise\RejectedPromise($this->prepareErrorData($exception));
        });
    }
    private function createGuzzleRequest(\NF_FU_VENDOR\Psr\Http\Message\RequestInterface $psrRequest, array $options)
    {
        $ringConfig = [];
        $statsCallback = isset($options['http_stats_receiver']) ? $options['http_stats_receiver'] : null;
        unset($options['http_stats_receiver']);
        // Remove unsupported options.
        foreach (\array_keys($options) as $key) {
            if (!isset(self::$validOptions[$key])) {
                unset($options[$key]);
            }
        }
        // Handle delay option.
        if (isset($options['delay'])) {
            $ringConfig['delay'] = $options['delay'];
            unset($options['delay']);
        }
        // Prepare sink option.
        if (isset($options['sink'])) {
            $ringConfig['save_to'] = $options['sink'] instanceof \NF_FU_VENDOR\Psr\Http\Message\StreamInterface ? new \NF_FU_VENDOR\Aws\Handler\GuzzleV5\GuzzleStream($options['sink']) : $options['sink'];
            unset($options['sink']);
        }
        // Ensure that all requests are async and lazy like Guzzle 6.
        $options['future'] = 'lazy';
        // Create the Guzzle 5 request from the provided PSR7 request.
        $request = $this->client->createRequest($psrRequest->getMethod(), $psrRequest->getUri(), $options);
        if (\is_callable($statsCallback)) {
            $request->getEmitter()->on('end', function (\NF_FU_VENDOR\GuzzleHttp\Event\EndEvent $event) use($statsCallback) {
                $statsCallback($event->getTransferInfo());
            });
        }
        // For the request body, adapt the PSR stream to a Guzzle stream.
        $body = $psrRequest->getBody();
        if ($body->getSize() === 0) {
            $request->setBody(null);
        } else {
            $request->setBody(new \NF_FU_VENDOR\Aws\Handler\GuzzleV5\GuzzleStream($body));
        }
        $request->setHeaders($psrRequest->getHeaders());
        $request->setHeader('User-Agent', $request->getHeader('User-Agent') . ' ' . \NF_FU_VENDOR\GuzzleHttp\Client::getDefaultUserAgent());
        // Make sure the delay is configured, if provided.
        if ($ringConfig) {
            foreach ($ringConfig as $k => $v) {
                $request->getConfig()->set($k, $v);
            }
        }
        return $request;
    }
    private function createPsr7Response(\NF_FU_VENDOR\GuzzleHttp\Message\ResponseInterface $response)
    {
        if ($body = $response->getBody()) {
            $body = new \NF_FU_VENDOR\Aws\Handler\GuzzleV5\PsrStream($body);
        }
        return new \NF_FU_VENDOR\GuzzleHttp\Psr7\Response($response->getStatusCode(), $response->getHeaders(), $body, $response->getReasonPhrase());
    }
    private function prepareErrorData(\Exception $e)
    {
        $error = ['exception' => $e, 'connection_error' => \false, 'response' => null];
        if ($e instanceof \NF_FU_VENDOR\GuzzleHttp\Exception\ConnectException) {
            $error['connection_error'] = \true;
        }
        if ($e instanceof \NF_FU_VENDOR\GuzzleHttp\Exception\RequestException && $e->getResponse()) {
            $error['response'] = $this->createPsr7Response($e->getResponse());
        }
        return $error;
    }
}
