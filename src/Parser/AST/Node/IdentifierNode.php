<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\AST\Node;

use Twitf\ExpressionEngine\Parser\Exception\ParserException;

class IdentifierNode extends AbstractNode
{
    private string $name;

    public function __construct(string $name, int $line)
    {
        parent::__construct($line);
        $this->name = $name;
    }

    public function evaluate($context): mixed
    {
        if (!$context->hasVariable($this->name)) {
            throw new ParserException("Undefined variable: {$this->name}", $this->line);
        }
        return $context->getVariable($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}