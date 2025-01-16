<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser;

use Twitf\ExpressionEngine\Parser\AST\Node\AssignmentNode;
use Twitf\ExpressionEngine\Parser\AST\Node\BinaryOperatorNode;
use Twitf\ExpressionEngine\Parser\AST\Node\BlockNode;
use Twitf\ExpressionEngine\Parser\AST\Node\BooleanLiteralNode;
use Twitf\ExpressionEngine\Parser\AST\Node\FunctionCallNode;
use Twitf\ExpressionEngine\Parser\AST\Node\IdentifierNode;
use Twitf\ExpressionEngine\Parser\AST\Node\IfNode;
use Twitf\ExpressionEngine\Parser\AST\Node\NumberNode;
use Twitf\ExpressionEngine\Parser\AST\Node\ReturnNode;
use Twitf\ExpressionEngine\Parser\AST\Node\StringNode;
use Twitf\ExpressionEngine\Parser\AST\Node\TernaryNode;
use Twitf\ExpressionEngine\Parser\AST\Node\UnaryOperatorNode;
use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Parser\Exception\ParserException;
use Twitf\ExpressionEngine\Parser\Operator\OperatorPrecedence;
use Twitf\ExpressionEngine\Token\Token;
use Twitf\ExpressionEngine\Token\TokenStream;
use Twitf\ExpressionEngine\Parser\AST\Node\PropertyAccessNode;

class Parser implements ParserInterface
{
    private TokenStream $tokens;

    public function parse(TokenStream $tokens): NodeInterface
    {
        $this->tokens = $tokens;
        return $this->parseBlock();
    }

    /**
     * 解析代码块
     */
    private function parseBlock(): NodeInterface
    {
        $statements = [];
        $line       = $this->tokens->getCurrentLine();

        // 检查是否是代码块开始
        $isBlockStatement = $this->tokens->peek()?->value === '{';
        if ($isBlockStatement) {
            $this->consume('Symbol', '{');
        }

        while (!$this->tokens->eof()) {
            // 如果遇到代码块结束，退出循环
            if ($isBlockStatement && $this->tokens->peek()?->value === '}') {
                $this->consume('Symbol', '}');
                break;
            }

            $statement    = $this->parseStatement();
            $statements[] = $statement;

            // 修改分号检查逻辑
            if (!$this->tokens->eof() &&
                !($statement instanceof BlockNode) &&
                !($statement instanceof IfNode) &&  // 不为 if 语句
                $this->tokens->peek()?->value !== '}') {
                $this->consume('Symbol', ';');  // 使用 consume 而不是 match
            }
        }

        return new BlockNode($statements, $line);
    }

    /**
     * 解析单个语句
     */
    private function parseStatement(): NodeInterface
    {
        $token = $this->tokens->peek();

        if ($token === null) {
            throw new ParserException("Unexpected end of input", $this->tokens->getCurrentLine());
        }

        // 添加对 return 语句的处理
        if ($token->type === 'Keyword' && $token->value === 'return') {
            return $this->parseReturnStatement();
        }

        // 检查是否是变量赋值
        if ($token->type === 'Identifier' && $this->tokens->peek(1)?->value === '=') {
            return $this->parseAssignment();
        }

        // 解析if语句
        if ($token->type === 'Keyword' && $token->value === 'if') {
            return $this->parseIfStatement();
        }

        // 解析表达式语句
        $expr = $this->parseExpression();

        // 移除这里的分号检查，让 parseBlock 处理分号
        return $expr;
    }

    // 添加解析 return 语句的方法
    private function parseReturnStatement(): NodeInterface
    {
        $line = $this->tokens->getCurrentLine();
        $this->consume('Keyword', 'return');
        $expression = $this->parseExpression();
        return new ReturnNode($expression, $line);
    }

    private function parseAssignment(): NodeInterface
    {
        // 获取变量名和当前行号
        $token = $this->tokens->peek();
        $variableName = $token->value;
        $line = $token->line;

        $this->tokens->next(); // 跳过变量名

        // 消费等号
        if (!$this->match('Operator', '=')) {
            throw new ParserException(
                "Expected '=' in assignment",
                $line
            );
        }

        // 解析赋值表达式
        $value = $this->parseExpression();

        return new AssignmentNode($variableName, $value, $line);
    }

    /**
     * 解析if语句
     */
    private function parseIfStatement(): NodeInterface
    {
        $line = $this->tokens->getCurrentLine();
        $this->consume('Keyword', 'if');
        $this->consume('Symbol', '(');
        $condition = $this->parseExpression();
        $this->consume('Symbol', ')');

        // 解析 then 分支
        $thenBranch = $this->parseBlock();

        // 解析 else 分支（如果存在）
        $elseBranch = null;
        if ($this->tokens->peek()?->value === 'else') {
            $this->consume('Keyword', 'else');
            $elseBranch = $this->parseBlock();
        }

        return new IfNode($condition, $thenBranch, $elseBranch, $line);
    }

    /**
     * 解析表达式
     */
    private function parseExpression(int $precedence = 0): NodeInterface
    {
        $left = $this->parsePrimary();

        while (true) {
            $token = $this->tokens->peek();
            if (!$token) {
                break;
            }

            // 特殊处理三元运算符
            if ($token->type === 'Symbol' && $token->value === '?') {  // 修改这里的判断条件
                $currentPrecedence = $this->getOperatorPrecedence($token);
                if ($currentPrecedence <= $precedence) {
                    break;
                }
                $this->tokens->next(); // 消费 '?' 符号
                $left = $this->parseTernary($left);
                continue;
            }

            $currentPrecedence = $this->getOperatorPrecedence($token);
            if ($currentPrecedence <= $precedence) {
                break;
            }

            $this->tokens->next();
            $left = $this->parseBinaryOperator($left, $token);
        }

        return $left;
    }

    /**
     * 解析基本表达式
     */
    private function parsePrimary(): NodeInterface
    {
        $token = $this->tokens->peek();
        if (!$token) {
            throw new ParserException("Unexpected end of input", $this->tokens->getCurrentLine());
        }

        // 处理一元运算符
        if ($this->isUnaryOperator($token)) {
            $this->tokens->next();
            $operand = $this->parsePrimary();
            return new UnaryOperatorNode($token->value, $operand, $token->line);
        }

        // 处理括号表达式
        if ($token->type === 'Symbol' && $token->value === '(') {
            $this->tokens->next();
            $expr = $this->parseExpression();
            $this->consume('Symbol', ')');
            return $expr;
        }

        return $this->parseValue();
    }

    /**
     * 解析值（数字、字符串、标识符等）
     */
    private function parseValue(): NodeInterface
    {
        $token = $this->tokens->next();
        if (!$token) {
            throw new ParserException(
                "Unexpected end of input",
                $this->tokens->getCurrentLine()
            );
        }

        $node = match ($token->type) {
            'Number'     => new NumberNode((int)$token->value, $token->line),
            'Float'      => new NumberNode((float)$token->value, $token->line),
            'String'     => new StringNode($token->value, $token->line),
            'Identifier' => $this->parseIdentifierOrFunctionCall($token),
            'Keyword'    => $this->parseKeywordLiteral($token),
            default      => throw new ParserException("Unexpected token: {$token->type}", $token->line),
        };

        // 检查是否有属性访问
        while ($this->match('Symbol', '.')) {
            $propertyToken = $this->consume('Identifier');
            $node = new PropertyAccessNode($node, $propertyToken->value, $propertyToken->line);
        }

        return $node;
    }

    /**
     * 解析标识符或函数调用
     */
    private function parseIdentifierOrFunctionCall(Token $token): NodeInterface
    {
        // 如果后面跟着左括号，说明是函数调用
        if ($this->tokens->peek()?->value === '(') {
            $this->tokens->next(); // 消费左括号
            $arguments = $this->parseFunctionArguments();
            $this->consume('Symbol', ')');
            return new FunctionCallNode($token->value, $arguments, $token->line);
        }

        // 否则是普通变量
        return new IdentifierNode($token->value, $token->line);
    }

    /**
     * 解析函数参数
     * @return NodeInterface[]
     */
    private function parseFunctionArguments(): array
    {
        $arguments = [];

        if ($this->tokens->peek()?->value !== ')') {
            do {
                $arguments[] = $this->parseExpression();
            } while ($this->match('Symbol', ','));
        }

        return $arguments;
    }

    /**
     * 解析二元运算符
     */
    private function parseBinaryOperator(NodeInterface $left, Token $operatorToken): NodeInterface
    {
        $precedence = $this->getOperatorPrecedence($operatorToken);
        $right      = $this->parseExpression($precedence);

        return new BinaryOperatorNode(
            $left,
            $operatorToken->value,
            $right,
            $operatorToken->line
        );
    }

    /**
     * 解析三元运算符
     */
    private function parseTernary(NodeInterface $condition): NodeInterface
    {
        $line = $this->tokens->getCurrentLine();

        // 不需要消费 '?' 因为已经在 parseExpression 中消费了
        $thenBranch = $this->parseExpression(OperatorPrecedence::getPrecedence('?'));
        $this->consume('Symbol', ':');
        $elseBranch = $this->parseExpression(OperatorPrecedence::getPrecedence('?'));

        return new TernaryNode($condition, $thenBranch, $elseBranch, $line);
    }

    /**
     * 获取运算符优先级
     */
    private function getOperatorPrecedence(Token $token): int
    {
        // 添加对三元运算符符号的支持
        if ($token->type === 'Symbol' && in_array($token->value, ['?', ':'], true)) {
            return OperatorPrecedence::getPrecedence($token->value);
        }
        if ($token->type === 'Operator' || $token->type === 'DoubleOperator') {
            return OperatorPrecedence::getPrecedence($token->value);
        }
        return 0;
    }

    /**
     * 判断是否是一元运算符
     */
    private function isUnaryOperator(Token $token): bool
    {
        return $token->type === 'Operator' &&
            in_array($token->value, ['!', '~', '+', '-'], true);
    }

    /**
     * 匹配并消费指定类型和值的token
     */
    private function match(string $type, ?string $value = null): bool
    {
        $token = $this->tokens->peek();
        if (!$token) {
            return false;
        }

        if ($token->type !== $type) {
            return false;
        }

        if ($value !== null && $token->value !== $value) {
            return false;
        }

        $this->tokens->next();
        return true;
    }

    /**
     * 消费指定类型和值的token，如果不匹配则抛出异常
     */
    private function consume(string $type, ?string $value = null): Token
    {
        $token = $this->tokens->peek();
        if (!$token) {
            throw new ParserException(
                "Unexpected end of input, expected {$type}" . ($value ? " '{$value}'" : ""),
                $this->tokens->getCurrentLine()
            );
        }

        if ($token->type !== $type || ($value !== null && $token->value !== $value)) {
            throw new ParserException(
                "Expected {$type}" . ($value ? " '{$value}'" : "") .
                ", got {$token->type} '{$token->value}'",
                $token->line
            );
        }

        return $this->tokens->next();
    }

    /**
     * 解析关键字字面量（true/false）
     */
    private function parseKeywordLiteral(Token $token): NodeInterface
    {
        return match ($token->value) {
            'true', 'false' => new BooleanLiteralNode($token->value === 'true', $token->line),
            default         => throw new ParserException("Unexpected keyword: {$token->value}", $token->line),
        };
    }
}