<?php
// src/Parser/AST/Node/PropertyAccessNode.php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Parser\Exception\ParserException;

/**
 * 属性访问节点，用于访问对象的属性
 * 例如：obj.prop
 */
class PropertyAccessNode extends AbstractNode
{
    private NodeInterface $object;
    private string $property;

    public function __construct(NodeInterface $object, string $property, int $line)
    {
        parent::__construct($line);
        $this->object = $object;
        $this->property = $property;
    }

    public function evaluate($context): mixed
    {
        $object = $this->object->evaluate($context);

        if (!is_array($object)) {
            throw new ParserException(
                "Cannot access property '{$this->property}' on non-array value",
                $this->line
            );
        }

        if (!array_key_exists($this->property, $object)) {
            throw new ParserException(
                "Undefined property: {$this->property}",
                $this->line
            );
        }

        return $object[$this->property];
    }
}