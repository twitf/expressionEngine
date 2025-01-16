<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Enum\EnumTokenType;
use Twitf\ExpressionEngine\Lexer\CharReader;
use Twitf\ExpressionEngine\Token\Token;

class IdentifierState extends AbstractState
{
    public function canHandle(string $char): bool
    {
        return preg_match('/^[\p{Han}a-zA-Z_]$/u', $char) === 1;
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 收集字符
        $this->buffer .= $currentChar;
        
        // 预读下一个字符
        $nextChar = $reader->peek();
        
        // 判断是否结束
        if (!preg_match('/^[\p{Han}a-zA-Z0-9_]$/u', $nextChar)) {
            $this->isEnd = true;
        }
        
        // 如果已经结束，返回Token
        if ($this->isEnd) {
            $type = in_array($this->buffer, EnumToken::getKeywords()) ? 
                EnumTokenType::KEYWORD : 
                EnumTokenType::IDENTIFIER;
                
            $token = new Token(
                $type,
                $this->buffer,
                $reader->getLine(),
                $reader->getColumn() - mb_strlen($this->buffer)
            );
            
            $this->reset();
            return [$token, EnumDfaState::S_RESET];
        }
        
        return [null, EnumDfaState::S_IDENTIFIER];
    }
}