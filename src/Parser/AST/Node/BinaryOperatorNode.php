<?php
// src/Parser/AST/Node/BinaryOperatorNode.php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Parser\Exception\ParserException;

class BinaryOperatorNode extends AbstractNode
{
    private NodeInterface $left;
    private string        $operator;
    private NodeInterface $right;

    public function __construct(NodeInterface $left, string $operator, NodeInterface $right, int $line)
    {
        parent::__construct($line);
        $this->left     = $left;
        $this->operator = $operator;
        $this->right    = $right;
    }

    public function evaluate($context): mixed
    {
        $left = $this->left->evaluate($context);

        // 短路逻辑运算符的特殊处理
        if ($this->operator === '&&' && !$left) {
            return false;
        }
        if ($this->operator === '||' && $left) {
            return true;
        }

        $right = $this->right->evaluate($context);
        
        // 处理字符串比较
        if (is_string($left) && is_string($right)) {
            return match ($this->operator) {
                '=='    => $left === $right,
                '!='    => $left !== $right,
                '>'     => $left > $right,
                '>='    => $left >= $right,
                '<'     => $left < $right,
                '<='    => $left <= $right,
                default => throw new ParserException("Invalid operator for strings: {$this->operator}", $this->line),
            };
        }

        return match ($this->operator) {
            '+'     => $left + $right,
            '-'     => $left - $right,
            '*'     => $left * $right,
            '/'     => $this->divide($left, $right),
            '%'     => $left % $right,
            '=='    => $left == $right,
            '!='    => $left != $right,
            '>'     => $left > $right,
            '>='    => $left >= $right,
            '<'     => $left < $right,
            '<='    => $left <= $right,
            '&&'    => $left && $right,
            '||'    => $left || $right,
            '&'     => $left & $right,
            '|'     => $left | $right,
            '^'     => $left ^ $right,
            '<<'    => $left << $right,
            '>>'    => $left >> $right,
            '='     => $this->handleAssignment($context, $right),
            default => throw new ParserException("Unknown operator: {$this->operator}", $this->line),
        };
    }

    private function divide($left, $right): float|int
    {
        if ($right === 0) {
            throw new ParserException("Division by zero", $this->line);
        }
        return $left / $right;
    }

    private function handleAssignment($context, $value): mixed
    {
        if (!$this->left instanceof IdentifierNode) {
            throw new ParserException(
                "Left side of assignment must be a variable",
                $this->line
            );
        }

        $context->setVariable($this->left->getName(), $value);
        return $value;
    }
}