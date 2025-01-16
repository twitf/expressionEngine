<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;

class IfNode extends AbstractNode
{
    private NodeInterface $condition;
    private NodeInterface $thenBranch;
    private ?NodeInterface $elseBranch;

    public function __construct(
        NodeInterface $condition,
        NodeInterface $thenBranch,
        ?NodeInterface $elseBranch,
        int $line
    ) {
        parent::__construct($line);
        $this->condition = $condition;
        $this->thenBranch = $thenBranch;
        $this->elseBranch = $elseBranch;
    }

    public function evaluate($context): mixed
    {
        $conditionResult = $this->condition->evaluate($context);

        if ($conditionResult) {
            return $this->thenBranch->evaluate($context);
        }

        return $this->elseBranch?->evaluate($context);
    }
}