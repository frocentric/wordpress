<?php

namespace NF_FU_VENDOR\Aws\Api\Serializer;

use NF_FU_VENDOR\Aws\Api\MapShape;
use NF_FU_VENDOR\Aws\Api\Service;
use NF_FU_VENDOR\Aws\Api\Operation;
use NF_FU_VENDOR\Aws\Api\Shape;
use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Aws\Api\TimestampShape;
use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\GuzzleHttp\Psr7;
use NF_FU_VENDOR\GuzzleHttp\Psr7\Uri;
use NF_FU_VENDOR\GuzzleHttp\Psr7\UriResolver;
use NF_FU_VENDOR\Psr\Http\Message\RequestInterface;
/**
 * Serializes HTTP locations like header, uri, payload, etc...
 * @internal
 */
abstract class RestSerializer
{
    /** @var Service */
    private $api;
    /** @var Psr7\Uri */
    private $endpoint;
    /**
     * @param Service $api      Service API description
     * @param string  $endpoint Endpoint to connect to
     */
    public function __construct(\NF_FU_VENDOR\Aws\Api\Service $api, $endpoint)
    {
        $this->api = $api;
        $this->endpoint = \NF_FU_VENDOR\GuzzleHttp\Psr7\uri_for($endpoint);
    }
    /**
     * @param CommandInterface $command Command to serialized
     *
     * @return RequestInterface
     */
    public function __invoke(\NF_FU_VENDOR\Aws\CommandInterface $command)
    {
        $operation = $this->api->getOperation($command->getName());
        $args = $command->toArray();
        $opts = $this->serialize($operation, $args);
        $uri = $this->buildEndpoint($operation, $args, $opts);
        return new \NF_FU_VENDOR\GuzzleHttp\Psr7\Request($operation['http']['method'], $uri, isset($opts['headers']) ? $opts['headers'] : [], isset($opts['body']) ? $opts['body'] : null);
    }
    /**
     * Modifies a hash of request options for a payload body.
     *
     * @param StructureShape   $member  Member to serialize
     * @param array            $value   Value to serialize
     * @param array            $opts    Request options to modify.
     */
    protected abstract function payload(\NF_FU_VENDOR\Aws\Api\StructureShape $member, array $value, array &$opts);
    private function serialize(\NF_FU_VENDOR\Aws\Api\Operation $operation, array $args)
    {
        $opts = [];
        $input = $operation->getInput();
        // Apply the payload trait if present
        if ($payload = $input['payload']) {
            $this->applyPayload($input, $payload, $args, $opts);
        }
        foreach ($args as $name => $value) {
            if ($input->hasMember($name)) {
                $member = $input->getMember($name);
                $location = $member['location'];
                if (!$payload && !$location) {
                    $bodyMembers[$name] = $value;
                } elseif ($location == 'header') {
                    $this->applyHeader($name, $member, $value, $opts);
                } elseif ($location == 'querystring') {
                    $this->applyQuery($name, $member, $value, $opts);
                } elseif ($location == 'headers') {
                    $this->applyHeaderMap($name, $member, $value, $opts);
                }
            }
        }
        if (isset($bodyMembers)) {
            $this->payload($operation->getInput(), $bodyMembers, $opts);
        }
        return $opts;
    }
    private function applyPayload(\NF_FU_VENDOR\Aws\Api\StructureShape $input, $name, array $args, array &$opts)
    {
        if (!isset($args[$name])) {
            return;
        }
        $m = $input->getMember($name);
        if ($m['streaming'] || ($m['type'] == 'string' || $m['type'] == 'blob')) {
            // Streaming bodies or payloads that are strings are
            // always just a stream of data.
            $opts['body'] = \NF_FU_VENDOR\GuzzleHttp\Psr7\stream_for($args[$name]);
            return;
        }
        $this->payload($m, $args[$name], $opts);
    }
    private function applyHeader($name, \NF_FU_VENDOR\Aws\Api\Shape $member, $value, array &$opts)
    {
        if ($member->getType() === 'timestamp') {
            $timestampFormat = !empty($member['timestampFormat']) ? $member['timestampFormat'] : 'rfc822';
            $value = \NF_FU_VENDOR\Aws\Api\TimestampShape::format($value, $timestampFormat);
        }
        if ($member['jsonvalue']) {
            $value = \json_encode($value);
            if (empty($value) && \JSON_ERROR_NONE !== \json_last_error()) {
                throw new \InvalidArgumentException('Unable to encode the provided value' . ' with \'json_encode\'. ' . \json_last_error_msg());
            }
            $value = \base64_encode($value);
        }
        $opts['headers'][$member['locationName'] ?: $name] = $value;
    }
    /**
     * Note: This is currently only present in the Amazon S3 model.
     */
    private function applyHeaderMap($name, \NF_FU_VENDOR\Aws\Api\Shape $member, array $value, array &$opts)
    {
        $prefix = $member['locationName'];
        foreach ($value as $k => $v) {
            $opts['headers'][$prefix . $k] = $v;
        }
    }
    private function applyQuery($name, \NF_FU_VENDOR\Aws\Api\Shape $member, $value, array &$opts)
    {
        if ($member instanceof \NF_FU_VENDOR\Aws\Api\MapShape) {
            $opts['query'] = isset($opts['query']) && \is_array($opts['query']) ? $opts['query'] + $value : $value;
        } elseif ($value !== null) {
            $type = $member->getType();
            if ($type === 'boolean') {
                $value = $value ? 'true' : 'false';
            } elseif ($type === 'timestamp') {
                $timestampFormat = !empty($member['timestampFormat']) ? $member['timestampFormat'] : 'iso8601';
                $value = \NF_FU_VENDOR\Aws\Api\TimestampShape::format($value, $timestampFormat);
            }
            $opts['query'][$member['locationName'] ?: $name] = $value;
        }
    }
    private function buildEndpoint(\NF_FU_VENDOR\Aws\Api\Operation $operation, array $args, array $opts)
    {
        $varspecs = [];
        // Create an associative array of varspecs used in expansions
        foreach ($operation->getInput()->getMembers() as $name => $member) {
            if ($member['location'] == 'uri') {
                $varspecs[$member['locationName'] ?: $name] = isset($args[$name]) ? $args[$name] : null;
            }
        }
        $relative = \preg_replace_callback('/\\{([^\\}]+)\\}/', function (array $matches) use($varspecs) {
            $isGreedy = \substr($matches[1], -1, 1) == '+';
            $k = $isGreedy ? \substr($matches[1], 0, -1) : $matches[1];
            if (!isset($varspecs[$k])) {
                return '';
            }
            if ($isGreedy) {
                return \str_replace('%2F', '/', \rawurlencode($varspecs[$k]));
            }
            return \rawurlencode($varspecs[$k]);
        }, $operation['http']['requestUri']);
        // Add the query string variables or appending to one if needed.
        if (!empty($opts['query'])) {
            $append = \NF_FU_VENDOR\GuzzleHttp\Psr7\build_query($opts['query']);
            $relative .= \strpos($relative, '?') ? "&{$append}" : "?{$append}";
        }
        // If endpoint has path, remove leading '/' to preserve URI resolution.
        $path = $this->endpoint->getPath();
        if ($path && $relative[0] === '/') {
            $relative = \substr($relative, 1);
        }
        // Expand path place holders using Amazon's slightly different URI
        // template syntax.
        return \NF_FU_VENDOR\GuzzleHttp\Psr7\UriResolver::resolve($this->endpoint, new \NF_FU_VENDOR\GuzzleHttp\Psr7\Uri($relative));
    }
}
