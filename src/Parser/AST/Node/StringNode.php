<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

class StringNode extends AbstractNode
{
    private string $value;

    public function __construct(string $value, int $line)
    {
        parent::__construct($line);
        $this->value = trim($value, '"\'');
    }

    public function evaluate($context): string
    {
        return $this->value;
    }
}