<?php

namespace NF_FU_VENDOR\Aws\Api\ErrorParser;

use NF_FU_VENDOR\Aws\Api\Parser\PayloadParserTrait;
use NF_FU_VENDOR\Aws\Api\Parser\XmlParser;
use NF_FU_VENDOR\Aws\Api\Service;
use NF_FU_VENDOR\Aws\Api\StructureShape;
use NF_FU_VENDOR\Aws\CommandInterface;
use NF_FU_VENDOR\Psr\Http\Message\ResponseInterface;
/**
 * Parses XML errors.
 */
class XmlErrorParser extends \NF_FU_VENDOR\Aws\Api\ErrorParser\AbstractErrorParser
{
    use PayloadParserTrait;
    protected $parser;
    public function __construct(\NF_FU_VENDOR\Aws\Api\Service $api = null, \NF_FU_VENDOR\Aws\Api\Parser\XmlParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new \NF_FU_VENDOR\Aws\Api\Parser\XmlParser();
    }
    public function __invoke(\NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response, \NF_FU_VENDOR\Aws\CommandInterface $command = null)
    {
        $code = (string) $response->getStatusCode();
        $data = ['type' => $code[0] == '4' ? 'client' : 'server', 'request_id' => null, 'code' => null, 'message' => null, 'parsed' => null];
        $body = $response->getBody();
        if ($body->getSize() > 0) {
            $this->parseBody($this->parseXml($body, $response), $data);
        } else {
            $this->parseHeaders($response, $data);
        }
        $this->populateShape($data, $response, $command);
        return $data;
    }
    private function parseHeaders(\NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response, array &$data)
    {
        if ($response->getStatusCode() == '404') {
            $data['code'] = 'NotFound';
        }
        $data['message'] = $response->getStatusCode() . ' ' . $response->getReasonPhrase();
        if ($requestId = $response->getHeaderLine('x-amz-request-id')) {
            $data['request_id'] = $requestId;
            $data['message'] .= " (Request-ID: {$requestId})";
        }
    }
    private function parseBody(\SimpleXMLElement $body, array &$data)
    {
        $data['parsed'] = $body;
        $prefix = $this->registerNamespacePrefix($body);
        if ($tempXml = $body->xpath("//{$prefix}Code[1]")) {
            $data['code'] = (string) $tempXml[0];
        }
        if ($tempXml = $body->xpath("//{$prefix}Message[1]")) {
            $data['message'] = (string) $tempXml[0];
        }
        $tempXml = $body->xpath("//{$prefix}RequestId[1]");
        if (isset($tempXml[0])) {
            $data['request_id'] = (string) $tempXml[0];
        }
    }
    protected function registerNamespacePrefix(\SimpleXMLElement $element)
    {
        $namespaces = $element->getDocNamespaces();
        if (!isset($namespaces[''])) {
            return '';
        }
        // Account for the default namespace being defined and PHP not
        // being able to handle it :(.
        $element->registerXPathNamespace('ns', $namespaces['']);
        return 'ns:';
    }
    protected function payload(\NF_FU_VENDOR\Psr\Http\Message\ResponseInterface $response, \NF_FU_VENDOR\Aws\Api\StructureShape $member)
    {
        $xmlBody = $this->parseXml($response->getBody(), $response);
        $prefix = $this->registerNamespacePrefix($xmlBody);
        $errorBody = $xmlBody->xpath("//{$prefix}Error");
        if (\is_array($errorBody) && !empty($errorBody[0])) {
            return $this->parser->parse($member, $errorBody[0]);
        }
    }
}
