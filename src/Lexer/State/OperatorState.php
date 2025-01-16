<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Enum\EnumTokenType;
use Twitf\ExpressionEngine\Lexer\CharReader;
use Twitf\ExpressionEngine\Token\Token;

class OperatorState extends AbstractState
{
    public function canHandle(string $char): bool
    {
        return in_array($char, EnumToken::getOperators());
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        $this->buffer = $currentChar;
        $nextChar = $reader->peek();
        
        // 检查是否可能形成三字符运算符 (!==, ===)
        if (($currentChar === '!' || $currentChar === '=') && $nextChar === '=') {
            $nextNextChar = $reader->peek(2);  // 看第三个字符
            if ($nextNextChar === '=') {
                $this->buffer .= $nextChar . $nextNextChar;
                $reader->read();  // 消费第二个字符
                $reader->read();  // 消费第三个字符
                $token = new Token(
                    EnumTokenType::DOUBLE_OPERATOR,
                    $this->buffer,
                    $reader->getLine(),
                    $reader->getColumn() - 3
                );
                $this->reset();
                return [$token, EnumDfaState::S_RESET];
            }
        }
        
        // 检查是否可能形成双字符运算符
        $possibleOp = $currentChar . $nextChar;
        if ($nextChar !== '' && in_array($possibleOp, EnumToken::getDoubleOperators(), true)) {
            $this->buffer = $currentChar;  // 保存第一个字符
            $nextStateHandler = $this->states[EnumDfaState::S_DOUBLE_OPERATOR];
            $nextStateHandler->setBuffer($this->buffer);  // 传递 buffer
            return [null, EnumDfaState::S_DOUBLE_OPERATOR];
        }
        
        // 单字符运算符
        $token = new Token(
            EnumTokenType::OPERATOR,
            $this->buffer,
            $reader->getLine(),
            $reader->getColumn() - 1
        );
        
        $this->reset();
        return [$token, EnumDfaState::S_RESET];
    }
}