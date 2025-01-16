<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Lexer\Exception\LexerException;
use Twitf\ExpressionEngine\Lexer\CharReader;

class MultiCommentState extends AbstractState
{
    private bool $isEnding = false;
    
    public function canHandle(string $char): bool
    {
        return $char === '*' && $this->buffer === '/';
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 如果是注释开始的第一个斜杠
        if (empty($this->buffer)) {
            $this->buffer = $currentChar;
            return [null, EnumDfaState::S_MULTI_COMMENT];
        }
        
        // 处理注释结束序列
        if ($currentChar === '*') {
            $this->isEnding = true;
            $this->buffer .= $currentChar;
            return [null, EnumDfaState::S_MULTI_COMMENT];
        }
        
        if ($this->isEnding && $currentChar === '/') {
            $this->reset();
            return [null, EnumDfaState::S_RESET];
        }
        
        // 如果不是结束序列，继续收集注释内容
        $this->isEnding = false;
        $this->buffer .= $currentChar;
        
        // 检查文件是否结束（防止未闭合的多行注释）
        if ($reader->peek() === '') {
            throw new LexerException(
                $reader->getLine(),
                $reader->getColumn(),
                '未闭合的多行注释'
            );
        }
        
        return [null, EnumDfaState::S_MULTI_COMMENT];
    }
    
    public function reset(): void
    {
        parent::reset();
        $this->isEnding = false;
    }
} 