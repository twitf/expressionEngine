<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;

abstract class AbstractNode implements NodeInterface
{
    /**
     * 节点所在的行号
     */
    protected int $line;

    public function __construct(int $line)
    {
        $this->line = $line;
    }

    /**
     * 获取节点所在行号
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * 获取节点类型（默认使用类的短名称）
     */
    public function getType(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}