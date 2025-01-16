<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

class NumberNode extends AbstractNode
{
    private float|int $value;

    public function __construct(float|int $value, int $line)
    {
        parent::__construct($line);
        $this->value = $value;
    }

    public function evaluate($context): float|int
    {
        return $this->value;
    }
}