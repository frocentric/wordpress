<?php

namespace NF_FU_VENDOR\Aws\Api\Parser;

use NF_FU_VENDOR\Aws\Api\DateTimeResult;
use NF_FU_VENDOR\Aws\Api\ListShape;
use NF_FU_VENDOR\Aws\Api\MapShape;
use NF_FU_VENDOR\Aws\Api\Shape;
use NF_FU_VENDOR\Aws\Api\StructureShape;
/**
 * @internal Implements standard XML parsing for REST-XML and Query protocols.
 */
class XmlParser
{
    public function parse(\NF_FU_VENDOR\Aws\Api\StructureShape $shape, \SimpleXMLElement $value)
    {
        return $this->dispatch($shape, $value);
    }
    private function dispatch($shape, \SimpleXMLElement $value)
    {
        static $methods = ['structure' => 'parse_structure', 'list' => 'parse_list', 'map' => 'parse_map', 'blob' => 'parse_blob', 'boolean' => 'parse_boolean', 'integer' => 'parse_integer', 'float' => 'parse_float', 'double' => 'parse_float', 'timestamp' => 'parse_timestamp'];
        $type = $shape['type'];
        if (isset($methods[$type])) {
            return $this->{$methods[$type]}($shape, $value);
        }
        return (string) $value;
    }
    private function parse_structure(\NF_FU_VENDOR\Aws\Api\StructureShape $shape, \SimpleXMLElement $value)
    {
        $target = [];
        foreach ($shape->getMembers() as $name => $member) {
            // Extract the name of the XML node
            $node = $this->memberKey($member, $name);
            if (isset($value->{$node})) {
                $target[$name] = $this->dispatch($member, $value->{$node});
            } else {
                $memberShape = $shape->getMember($name);
                if (!empty($memberShape['xmlAttribute'])) {
                    $target[$name] = $this->parse_xml_attribute($shape, $memberShape, $value);
                }
            }
        }
        return $target;
    }
    private function memberKey(\NF_FU_VENDOR\Aws\Api\Shape $shape, $name)
    {
        if (null !== $shape['locationName']) {
            return $shape['locationName'];
        }
        if ($shape instanceof \NF_FU_VENDOR\Aws\Api\ListShape && $shape['flattened']) {
            return $shape->getMember()['locationName'] ?: $name;
        }
        return $name;
    }
    private function parse_list(\NF_FU_VENDOR\Aws\Api\ListShape $shape, \SimpleXMLElement $value)
    {
        $target = [];
        $member = $shape->getMember();
        if (!$shape['flattened']) {
            $value = $value->{$member['locationName'] ?: 'member'};
        }
        foreach ($value as $v) {
            $target[] = $this->dispatch($member, $v);
        }
        return $target;
    }
    private function parse_map(\NF_FU_VENDOR\Aws\Api\MapShape $shape, \SimpleXMLElement $value)
    {
        $target = [];
        if (!$shape['flattened']) {
            $value = $value->entry;
        }
        $mapKey = $shape->getKey();
        $mapValue = $shape->getValue();
        $keyName = $shape->getKey()['locationName'] ?: 'key';
        $valueName = $shape->getValue()['locationName'] ?: 'value';
        foreach ($value as $node) {
            $key = $this->dispatch($mapKey, $node->{$keyName});
            $value = $this->dispatch($mapValue, $node->{$valueName});
            $target[$key] = $value;
        }
        return $target;
    }
    private function parse_blob(\NF_FU_VENDOR\Aws\Api\Shape $shape, $value)
    {
        return \base64_decode((string) $value);
    }
    private function parse_float(\NF_FU_VENDOR\Aws\Api\Shape $shape, $value)
    {
        return (double) (string) $value;
    }
    private function parse_integer(\NF_FU_VENDOR\Aws\Api\Shape $shape, $value)
    {
        return (int) (string) $value;
    }
    private function parse_boolean(\NF_FU_VENDOR\Aws\Api\Shape $shape, $value)
    {
        return $value == 'true';
    }
    private function parse_timestamp(\NF_FU_VENDOR\Aws\Api\Shape $shape, $value)
    {
        if (!empty($shape['timestampFormat']) && $shape['timestampFormat'] === 'unixTimestamp') {
            return \NF_FU_VENDOR\Aws\Api\DateTimeResult::fromEpoch((string) $value);
        }
        return new \NF_FU_VENDOR\Aws\Api\DateTimeResult($value);
    }
    private function parse_xml_attribute(\NF_FU_VENDOR\Aws\Api\Shape $shape, \NF_FU_VENDOR\Aws\Api\Shape $memberShape, $value)
    {
        $namespace = $shape['xmlNamespace']['uri'] ? $shape['xmlNamespace']['uri'] : '';
        $prefix = $shape['xmlNamespace']['prefix'] ? $shape['xmlNamespace']['prefix'] : '';
        if (!empty($prefix)) {
            $prefix .= ':';
        }
        $key = \str_replace($prefix, '', $memberShape['locationName']);
        $attributes = $value->attributes($namespace);
        return isset($attributes[$key]) ? (string) $attributes[$key] : null;
    }
}
