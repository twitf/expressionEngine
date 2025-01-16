<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\Exception;

use Exception;

class ParserException extends Exception
{
    public function __construct(string $message, int $line, int $code = 0, ?Exception $previous = null)
    {
        $this->line = $line; // 在调用父构造函数前设置行号
        $message    = sprintf('语法错误 Line %s: %s', $line, $message);
        parent::__construct($message, $code, $previous);
    }
}