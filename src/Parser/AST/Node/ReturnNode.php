<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Parser\Runtime\ReturnValue;

class ReturnNode extends AbstractNode
{
    private NodeInterface $expression;

    public function __construct(NodeInterface $expression, int $line)
    {
        parent::__construct($line);
        $this->expression = $expression;
    }

    public function evaluate($context): ReturnValue
    {
        $result = $this->expression->evaluate($context);
        return new ReturnValue($result);
    }
}