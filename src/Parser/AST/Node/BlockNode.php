<?php
// src/Parser/AST/Node/BlockNode.php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Parser\Runtime\ReturnValue;

/**
 * 代码块节点，用于处理多个语句
 */
class BlockNode extends AbstractNode
{
    /** @var NodeInterface[] */
    private array $statements;

    /**
     * @param NodeInterface[] $statements 语句节点数组
     * @param int $line 行号
     */
    public function __construct(array $statements, int $line)
    {
        parent::__construct($line);
        $this->statements = $statements;
    }

    public function evaluate($context): mixed
    {
        $result = null;
        foreach ($this->statements as $statement) {
            $result = $statement->evaluate($context);

            if ($result instanceof ReturnValue) {
                return $result->getValue();
            }
        }
        return $result;
    }
}