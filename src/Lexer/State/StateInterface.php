<?php

namespace Twitf\ExpressionEngine\Lexer\State;

use Twitf\ExpressionEngine\Lexer\CharReader;
use Twitf\ExpressionEngine\Token\Token;

interface StateInterface
{
    /**
     * 判断是否可以进入该状态
     */
    public function canHandle(string $char): bool;
    
    /**
     * 处理当前状态
     * @return array{Token|null, int} [Token|null, 下一个状态]
     */
    public function process(CharReader $reader, string $currentChar): array;
    
    /**
     * 获取当前缓冲区内容
     */
    public function getBuffer(): string;
    
    /**
     * 重置状态
     */
    public function reset(): void;
    
    /**
     * 判断状态是否结束
     */
    public function isEnd(): bool;
} 