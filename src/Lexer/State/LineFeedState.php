<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Lexer\CharReader;

class LineFeedState extends AbstractState
{
    public function canHandle(string $char): bool
    {
        return in_array($char, EnumToken::getLineFeed());
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 换行符不生成Token，直接返回重置状态
        return [null, EnumDfaState::S_RESET];
    }
}