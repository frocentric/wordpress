<?php

namespace NF_FU_VENDOR\Aws\Api;

/**
 * Represents a list shape.
 */
class ListShape extends \NF_FU_VENDOR\Aws\Api\Shape
{
    private $member;
    public function __construct(array $definition, \NF_FU_VENDOR\Aws\Api\ShapeMap $shapeMap)
    {
        $definition['type'] = 'list';
        parent::__construct($definition, $shapeMap);
    }
    /**
     * @return Shape
     * @throws \RuntimeException if no member is specified
     */
    public function getMember()
    {
        if (!$this->member) {
            if (!isset($this->definition['member'])) {
                throw new \RuntimeException('No member attribute specified');
            }
            $this->member = \NF_FU_VENDOR\Aws\Api\Shape::create($this->definition['member'], $this->shapeMap);
        }
        return $this->member;
    }
}
