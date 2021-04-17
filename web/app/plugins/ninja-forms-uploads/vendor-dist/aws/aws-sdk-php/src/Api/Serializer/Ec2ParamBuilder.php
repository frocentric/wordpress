<?php

namespace NF_FU_VENDOR\Aws\Api\Serializer;

use NF_FU_VENDOR\Aws\Api\Shape;
use NF_FU_VENDOR\Aws\Api\ListShape;
/**
 * @internal
 */
class Ec2ParamBuilder extends \NF_FU_VENDOR\Aws\Api\Serializer\QueryParamBuilder
{
    protected function queryName(\NF_FU_VENDOR\Aws\Api\Shape $shape, $default = null)
    {
        return $shape['queryName'] ?: \ucfirst($shape['locationName']) ?: $default;
    }
    protected function isFlat(\NF_FU_VENDOR\Aws\Api\Shape $shape)
    {
        return \false;
    }
    protected function format_list(\NF_FU_VENDOR\Aws\Api\ListShape $shape, array $value, $prefix, &$query)
    {
        // Handle empty list serialization
        if (!$value) {
            $query[$prefix] = \false;
        } else {
            $items = $shape->getMember();
            foreach ($value as $k => $v) {
                $this->format($items, $v, $prefix . '.' . ($k + 1), $query);
            }
        }
    }
}
