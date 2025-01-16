<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Enum\EnumTokenType;
use Twitf\ExpressionEngine\Lexer\CharReader;
use Twitf\ExpressionEngine\Token\Token;

class DoubleOperatorState extends AbstractState
{
    public function canHandle(string $char): bool
    {
        // 检查是否可能是双字符运算符的第二个字符
        if (empty($this->buffer)) {
            return false;
        }
        return in_array($this->buffer . $char, EnumToken::getDoubleOperators());
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 如果 buffer 为空，这是第一个字符
        if (empty($this->buffer)) {
            $this->buffer = $currentChar;
            return [null, EnumDfaState::S_DOUBLE_OPERATOR];
        }

        // 收集第二个字符并检查是否是有效的双字符运算符
        $operator = $this->buffer . $currentChar;
        if (in_array($operator, EnumToken::getDoubleOperators(), true)) {
            $token = new Token(
                EnumTokenType::DOUBLE_OPERATOR,
                $operator,
                $reader->getLine(),
                $reader->getColumn() - 2
            );
            $this->reset();
            return [$token, EnumDfaState::S_RESET];
        }

        // 如果不是有效的双字符运算符，回退并生成单字符运算符
        $reader->backup();
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