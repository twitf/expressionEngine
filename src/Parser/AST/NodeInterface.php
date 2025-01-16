<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST;

use Twitf\ExpressionEngine\Parser\Context\Context;

interface NodeInterface
{
    /**
     * 执行节点求值
     * @param mixed $context 上下文环境
     * @return mixed 求值结果
     */
    public function evaluate(Context $context): mixed;

    /**
     * 获取节点类型
     * @return string
     */
    public function getType(): string;
}