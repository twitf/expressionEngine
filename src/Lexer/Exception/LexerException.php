<?php

namespace Twitf\ExpressionEngine\Lexer\Exception;

class LexerException extends \Exception
{
    public function __construct(int $line, int $column, string $message)
    {
        parent::__construct(sprintf('词法错误 [%d:%d]: %s', $line, $column, $message));
    }
}