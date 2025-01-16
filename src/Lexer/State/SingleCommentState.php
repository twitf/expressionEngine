<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Lexer\CharReader;

class SingleCommentState extends AbstractState
{
    public function canHandle(string $char): bool
    {
        return $char === '/' && $this->buffer === '/';
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 如果是注释开始的第一个斜杠
        if (empty($this->buffer)) {
            $this->buffer = $currentChar;
            return [null, EnumDfaState::S_SINGLE_COMMENT];
        }
        
        // 如果遇到换行，结束注释状态
        if ($currentChar === "\n") {
            $this->reset();
            return [null, EnumDfaState::S_RESET];
        }
        
        // 继续收集注释内容（实际上不会生成Token）
        $this->buffer .= $currentChar;
        return [null, EnumDfaState::S_SINGLE_COMMENT];
    }
} 