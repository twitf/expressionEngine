<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Enum\EnumTokenType;
use Twitf\ExpressionEngine\Lexer\Exception\LexerException;
use Twitf\ExpressionEngine\Lexer\CharReader;
use Twitf\ExpressionEngine\Token\Token;

class StringState extends AbstractState
{
    private string $quoteChar = '';
    private int $startColumn = 0;
    private bool $isEscaped = false;
    private bool $isComplete = false;
    
    public function canHandle(string $char): bool
    {
        return $char === '"' || $char === "'";
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 如果 buffer 为空，这是第一个字符
        if (empty($this->buffer)) {
            $this->quoteChar = $currentChar;
            $this->buffer = $currentChar;
            $this->startColumn = $reader->getStartColumn();
            $this->isEscaped = false;
            return [null, EnumDfaState::S_STRING];
        }

        // 收集字符
        $this->buffer .= $currentChar;

        // 如果遇到相同的引号，且不是转义状态，则结束
        if ($currentChar === $this->quoteChar && !$this->isEscaped) {
            $value = stripcslashes($this->buffer);
            $this->reset();
            return [
                new Token(
                    EnumTokenType::STRING,
                    $value,
                    $reader->getLine(),
                    $this->startColumn
                ),
                EnumDfaState::S_RESET
            ];
        }

        // 处理转义字符
        if ($currentChar === '\\') {
            $this->isEscaped = true;
            return [null, EnumDfaState::S_STRING];
        }

        // 重置转义状态
        $this->isEscaped = false;

        // 如果遇到换行符，说明字符串未闭合就结束了
        if (in_array($currentChar, EnumToken::getLineFeed())) {
            throw new LexerException(
                $reader->getLine(),
                $this->startColumn,
                sprintf(
                    '未闭合的字符串: "%s"',
                    rtrim($this->buffer, "\r\n")
                )
            );
        }

        return [null, EnumDfaState::S_STRING];
    }
    
    public function reset(): void
    {
        parent::reset();
        $this->quoteChar = '';
        $this->startColumn = 0;
        $this->isEscaped = false;
    }
    
    public function isUnclosed(): bool
    {
        // 如果 buffer 为空或字符串已完整，就不是未闭合的
        if (empty($this->buffer) || $this->isComplete) {
            return false;
        }
        
        // 否则检查最后一个字符是否是引号
        return $this->buffer[strlen($this->buffer) - 1] !== $this->quoteChar;
    }
}