<?php

namespace Twitf\ExpressionEngine\Lexer\State;

abstract class AbstractState implements StateInterface
{
    protected string $buffer = '';
    protected array $states = [];
    protected bool $isEnd = false;
    
    public function setStates(array $states): void
    {
        $this->states = $states;
    }
    
    public function getBuffer(): string
    {
        return $this->buffer;
    }
    
    public function reset(): void
    {
        $this->buffer = '';
        $this->isEnd = false;
    }
    
    public function isEnd(): bool
    {
        return $this->isEnd;
    }
    
    /**
     * 设置初始buffer，用于状态切换时传递字符
     */
    public function setBuffer(string $buffer): void
    {
        $this->buffer = $buffer;
    }
}