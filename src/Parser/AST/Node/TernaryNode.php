<?php
// src/Parser/AST/Node/TernaryNode.php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;

/**
 * 三元运算符节点
 */
class TernaryNode extends AbstractNode
{
    private NodeInterface $condition;
    private NodeInterface $thenBranch;
    private NodeInterface $elseBranch;

    public function __construct(
        NodeInterface $condition,
        NodeInterface $thenBranch,
        NodeInterface $elseBranch,
        int $line
    ) {
        parent::__construct($line);
        $this->condition = $condition;
        $this->thenBranch = $thenBranch;
        $this->elseBranch = $elseBranch;
    }

    public function evaluate($context): mixed
    {
        return $this->condition->evaluate($context)
            ? $this->thenBranch->evaluate($context)
            : $this->elseBranch->evaluate($context);
    }
}