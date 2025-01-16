<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumTokenType;
use Twitf\ExpressionEngine\Lexer\CharReader;
use Twitf\ExpressionEngine\Token\Token;

class NumberState extends AbstractState
{
    private bool $hasDecimalPoint = false;
    
    public function canHandle(string $char): bool
    {
        return is_numeric($char);
    }
    
    public function process(CharReader $reader, string $currentChar): array
    {
        // 如果当前字符不是数字，说明数字已经结束
        if (!is_numeric($currentChar)) {
            // 先生成数字Token
            $token = new Token(
                $this->hasDecimalPoint ? EnumTokenType::FLOAT : EnumTokenType::NUMBER,
                $this->buffer,
                $reader->getLine(),
                $reader->getColumn() - mb_strlen($this->buffer)
            );
            
            $this->reset();
            // 回退一个字符，让下一次处理这个非数字字符
            $reader->backup();
            return [$token, EnumDfaState::S_RESET];
        }
        
        // 收集字符
        $this->buffer .= $currentChar;
        
        // 预读下一个字符
        $nextChar = $reader->peek();
        
        // 处理小数点
        if ($nextChar === '.') {
            if ($this->hasDecimalPoint) {
                throw new \RuntimeException('非法的数字格式：多个小数点');
            }
            $this->hasDecimalPoint = true;
            return [null, EnumDfaState::S_FLOAT];
        }
        
        // 判断是否结束
        if (!is_numeric($nextChar)) {
            $this->isEnd = true;
        }
        
        return [null, $this->hasDecimalPoint ? EnumDfaState::S_FLOAT : EnumDfaState::S_NUMBER];
    }
    
    public function reset(): void
    {
        parent::reset();
        $this->hasDecimalPoint = false;
    }
}