<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

class BooleanLiteralNode extends AbstractNode
{
    private bool $value;

    public function __construct(bool $value, int $line)
    {
        parent::__construct($line);
        $this->value = $value;
    }

    public function evaluate($context): bool
    {
        return $this->value;
    }
} 