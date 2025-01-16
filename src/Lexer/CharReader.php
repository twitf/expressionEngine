<?php

namespace Twitf\ExpressionEngine\Lexer;

class CharReader
{
    private string $input;
    private int $position = 0;
    private int $line = 1;
    private int $column = 0;
    private int $length;
    private string $lastChar = '';
    
    public function __construct(string $input)
    {
        $this->input = $input;
        $this->length = mb_strlen($input);
    }
    
    public function read(): string
    {
        if ($this->position >= $this->length) {
            return '';
        }
        
        $char = mb_substr($this->input, $this->position, 1);
        $this->position++;
        $this->lastChar = $char;
        
        if ($char === "\n") {
            $this->line++;
            $this->column = 0;
        } else {
            $this->column++;
        }
        
        return $char;
    }
    
    /**
     * 预读指定数量的字符
     * @param int $offset 预读的偏移量，默认为 1
     * @return string 预读的字符，如果到达文件末尾则返回空字符串
     */
    public function peek(int $offset = 1): string
    {
        $peekPosition = $this->position + $offset - 1;
        if ($peekPosition >= $this->length) {
            return '';
        }
        return mb_substr($this->input, $peekPosition, 1);
    }
    
    public function getLine(): int
    {
        return $this->line;
    }
    
    public function getColumn(): int
    {
        return $this->column;
    }
    
    /**
     * 回退一个字符
     */
    public function backup(): void
    {
        if ($this->position > 0) {
            $this->position--;
            if ($this->lastChar === "\n") {
                $this->line--;
                // 需要重新计算上一行的列数
                $lastNewline = strrpos(mb_substr($this->input, 0, $this->position), "\n");
                $this->column = $lastNewline === false ? $this->position : $this->position - $lastNewline - 1;
            } else {
                $this->column--;
            }
        }
    }
    
    /**
     * 获取当前字符的起始列号
     */
    public function getStartColumn(): int
    {
        return $this->column - 1;  // 当前列号减1就是起始列号
    }
} 