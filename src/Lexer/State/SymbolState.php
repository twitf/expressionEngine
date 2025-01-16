<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Enum\EnumTokenType;
use Twitf\ExpressionEngine\Lexer\CharReader;
use Twitf\ExpressionEngine\Token\Token;

class SymbolState extends AbstractState
{
    public function canHandle(string $char): bool
    {
        return in_array($char, EnumToken::getSymbols());
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        $this->buffer = $currentChar;
        
        $token = new Token(
            EnumTokenType::SYMBOL,
            $this->buffer,
            $reader->getLine(),
            $reader->getColumn() - mb_strlen($this->buffer)
        );
        
        $this->reset();
        return [$token, EnumDfaState::S_RESET];
    }
}