<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Parser\Context\Context;
use Twitf\ExpressionEngine\Parser\Exception\ParserException;

class FunctionCallNode extends AbstractNode
{
    private string $name;
    private array $arguments;

    /**
     * @param string $name 函数名
     * @param NodeInterface[] $arguments 参数节点数组
     * @param int $line 行号
     */
    public function __construct(string $name, array $arguments, int $line)
    {
        parent::__construct($line);
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function evaluate(Context $context): mixed
    {
        if (!$context->hasFunction($this->name)) {
            throw new ParserException("Undefined function: {$this->name}", $this->line);
        }

        // 计算所有参数的值
        $args = array_map(
            fn(NodeInterface $arg) => $arg->evaluate($context),
            $this->arguments
        );

        // 调用注册的函数
        return $context->callFunction($this->name, $args);
    }
}