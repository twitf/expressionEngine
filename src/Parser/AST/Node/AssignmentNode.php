<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

// 修改 AssignmentNode 以适配现有的 AST 结构
use Twitf\ExpressionEngine\Parser\AST\NodeInterface;

class AssignmentNode extends AbstractNode
{
    private string        $variableName;
    private NodeInterface $value;

    public function __construct(string $variableName, NodeInterface $value, int $line = 0)
    {
        parent::__construct($line);  // 调用父类构造函数
        $this->variableName = $variableName;
        $this->value        = $value;
    }

    public function evaluate(mixed $context): mixed
    {
        $value = $this->value->evaluate($context);
        $context->setVariable($this->variableName, $value);
        return $value;
    }

    public function getType(): string
    {
        return 'Assignment';
    }

    public function getChildren(): array
    {
        return [$this->value];
    }
}