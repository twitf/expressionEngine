<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Lexer\CharReader;

class WhitespaceState extends AbstractState
{
    public function canHandle(string $char): bool
    {
        return in_array($char, EnumToken::getWhitespace());
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 空白字符不生成Token，直接返回重置状态
        return [null, EnumDfaState::S_RESET];
    }
}