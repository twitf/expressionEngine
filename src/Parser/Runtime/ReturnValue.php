<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\Runtime;

class ReturnValue
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
} 