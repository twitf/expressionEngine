<?php

namespace Twitf\ExpressionEngine\Lexer;

use Twitf\ExpressionEngine\Enum\EnumDfaState;
use Twitf\ExpressionEngine\Enum\EnumToken;
use Twitf\ExpressionEngine\Lexer\Exception\LexerException;
use Twitf\ExpressionEngine\Lexer\State\DoubleOperatorState;
use Twitf\ExpressionEngine\Lexer\State\IdentifierState;
use Twitf\ExpressionEngine\Lexer\State\MultiCommentState;
use Twitf\ExpressionEngine\Lexer\State\NumberState;
use Twitf\ExpressionEngine\Lexer\State\OperatorState;
use Twitf\ExpressionEngine\Lexer\State\SingleCommentState;
use Twitf\ExpressionEngine\Lexer\State\StringState;
use Twitf\ExpressionEngine\Lexer\State\SymbolState;
use Twitf\ExpressionEngine\Lexer\State\WhitespaceState;
use Twitf\ExpressionEngine\Lexer\State\LineFeedState;
use Twitf\ExpressionEngine\Token\Token;

class Lexer
{
    private CharReader $reader;
    private array      $states;
    private int        $currentState = EnumDfaState::S_RESET;

    public function __construct(string $input)
    {
        $this->reader = new CharReader($input);
        $this->initializeStates();
    }

    /**
     * 初始化所有状态处理器
     */
    private function initializeStates(): void
    {
        $this->states = [
            EnumDfaState::S_IDENTIFIER      => new IdentifierState(),
            EnumDfaState::S_NUMBER          => new NumberState(),
            EnumDfaState::S_OPERATOR        => new OperatorState(),
            EnumDfaState::S_DOUBLE_OPERATOR => new DoubleOperatorState(),
            EnumDfaState::S_STRING          => new StringState(),
            EnumDfaState::S_SINGLE_COMMENT  => new SingleCommentState(),
            EnumDfaState::S_MULTI_COMMENT   => new MultiCommentState(),
            EnumDfaState::S_SYMBOL          => new SymbolState(),
            EnumDfaState::S_WHITESPACE      => new WhitespaceState(),
            EnumDfaState::S_LINEFEED        => new LineFeedState(),
        ];

        // 为每个状态设置所有状态的引用
        foreach ($this->states as $state) {
            $state->setStates($this->states);
        }
    }

    public function tokenize(): array
    {
        $tokens = [];
        while ($token = $this->nextToken()) {
            $tokens[] = $token;
        }

        // 检查是否有未闭合的状态
        if ($this->currentState !== EnumDfaState::S_RESET) {
            $state = $this->states[$this->currentState] ?? null;
            if ($state instanceof StringState) {
                // 只有当状态是 StringState 且 buffer 不为空时才检查
                if (!empty($state->getBuffer())) {
                    throw new LexerException(
                        $this->reader->getLine(),
                        $this->reader->getColumn() - 1,
                        sprintf(
                            '未闭合的字符串: "%s"',
                            $state->getBuffer()
                        )
                    );
                }
            }
            // 确保所有状态都被重置
            $state->reset();
            $this->currentState = EnumDfaState::S_RESET;
        }

        return $tokens;
    }

    private function nextToken(): ?Token
    {
        while (($char = $this->reader->read()) !== '') {
            // 重置状态下，确定下一个状态
            if ($this->currentState === EnumDfaState::S_RESET) {
                $this->currentState = $this->determineState($char);
                // 确保新状态的 buffer 是空的
                $this->states[$this->currentState]->reset();
            }

            // 获取当前状态处理器
            $state = $this->states[$this->currentState] ?? null;
            if (!$state) {
                throw new LexerException(
                    $this->reader->getLine(),
                    $this->reader->getColumn(),
                    "未知的状态: {$this->currentState}"
                );
            }

            // 处理当前字符
            [$token, $nextState] = $state->process($this->reader, $char);

            // 如果生成了 token，返回它
            if ($token) {
                // 如果当前状态不是 RESET，需要重置它
                if ($this->currentState !== EnumDfaState::S_RESET) {
                    $state->reset();
                    $this->currentState = EnumDfaState::S_RESET;
                }
                return $token;
            }

            // 更新状态
            $this->currentState = $nextState;
        }

        return null;
    }

    /**
     * 根据当前字符确定状态
     */
    private function determineState(string $char): int
    {
        // 检查空白字符
        if (in_array($char, EnumToken::getWhitespace())) {
            return EnumDfaState::S_WHITESPACE;
        }

        // 检查换行符
        if (in_array($char, EnumToken::getLineFeed())) {
            return EnumDfaState::S_LINEFEED;
        }

        // 检查标识符 , 是否是一个单一的汉字、拉丁字母或下划线
        if (preg_match('/^[\p{Han}a-zA-Z_]$/u', $char)) {
            return EnumDfaState::S_IDENTIFIER;
        }

        // 检查数字
        if (is_numeric($char)) {
            return EnumDfaState::S_NUMBER;
        }

        // 检查字符串
        if ($char === '"' || $char === "'") {
            return EnumDfaState::S_STRING;
        }

        // 检查运算符
        if (in_array($char, EnumToken::getOperators())) {
            // 检查是否是注释开始
            if ($char === '/' && $this->reader->peek() === '/') {
                return EnumDfaState::S_SINGLE_COMMENT;
            }
            if ($char === '/' && $this->reader->peek() === '*') {
                return EnumDfaState::S_MULTI_COMMENT;
            }
            return EnumDfaState::S_OPERATOR;
        }

        // 检查符号
        if (in_array($char, EnumToken::getSymbols())) {
            return EnumDfaState::S_SYMBOL;
        }

        throw new LexerException($this->reader->getLine(), $this->reader->getColumn(), "非法字符: {$char}");
    }
} 