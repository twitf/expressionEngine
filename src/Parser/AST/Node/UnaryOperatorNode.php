<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Parser\Exception\ParserException;

class UnaryOperatorNode extends AbstractNode
{
    private string $operator;
    private NodeInterface $operand;

    public function __construct(string $operator, NodeInterface $operand, int $line)
    {
        parent::__construct($line);
        $this->operator = $operator;
        $this->operand = $operand;
    }

    public function evaluate($context): mixed
    {
        $value = $this->operand->evaluate($context);

        return match ($this->operator) {
            '!' => !$value,
            '~' => ~$value,
            '-' => -$value,
            '+' => +$value,
            default => throw new ParserException("Unknown unary operator: {$this->operator}", $this->line),
        };
    }
}