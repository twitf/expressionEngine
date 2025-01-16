<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Token;

class Token
{
    public function __construct(
        public readonly string $type,
        public readonly string $value,
        public readonly int $line,
        public readonly int $column
    ) {
    }
}
